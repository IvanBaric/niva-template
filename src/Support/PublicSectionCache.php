<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Support;

use Closure;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

final class PublicSectionCache
{
    private const REQUEST_MEMO_KEY = 'niva.public-section-cache.memo';

    public function __construct(private readonly EloquentModelSnapshot $snapshots) {}

    public function enabled(): bool
    {
        return (bool) config('niva-template.public_section_cache.enabled', false);
    }

    public function staticTtl(): int
    {
        return max(1, (int) config('niva-template.public_section_cache.static_ttl', 31_536_000));
    }

    public function dynamicTtl(): int
    {
        return max(1, (int) config('niva-template.public_section_cache.dynamic_ttl', 3_600));
    }

    /**
     * @template TValue
     *
     * @param  array<string, mixed>  $context
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public function rememberSectionValue(
        int|string $teamId,
        string $sectionUuid,
        string $scope,
        array $context,
        int $ttl,
        Closure $callback,
    ): mixed {
        return $this->rememberValue($teamId, $sectionUuid, $scope, $context, $ttl, $callback);
    }

    /**
     * @template TValue
     *
     * @param  array<string, mixed>  $context
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    public function rememberTeamValue(int|string $teamId, string $scope, array $context, int $ttl, Closure $callback): mixed
    {
        return $this->rememberValue($teamId, null, $scope, $context, $ttl, $callback);
    }

    /**
     * @template TModel of Model
     *
     * @param  array<string, mixed>  $context
     * @param  class-string<TModel>  $expectedClass
     * @param  Closure(): EloquentCollection<int, TModel>  $callback
     * @return EloquentCollection<int, TModel>
     */
    public function rememberSectionModels(
        int|string $teamId,
        string $sectionUuid,
        string $scope,
        array $context,
        int $ttl,
        string $expectedClass,
        Closure $callback,
    ): EloquentCollection {
        if (! $this->enabled()) {
            return $callback();
        }

        $snapshot = $this->rememberSectionValue(
            $teamId,
            $sectionUuid,
            $scope,
            $context,
            $ttl,
            fn (): array => $this->snapshots->encodeCollection($callback()),
        );

        try {
            return $this->snapshots->decodeCollection((array) $snapshot, $expectedClass);
        } catch (Throwable) {
            $this->forgetDataKey($teamId, $sectionUuid, $scope, $context);
            $models = $callback();
            $this->putValue($teamId, $sectionUuid, $scope, $context, $ttl, $this->snapshots->encodeCollection($models));

            return $models;
        }
    }

    /**
     * @template TModel of Model
     *
     * @param  array<string, mixed>  $context
     * @param  class-string<TModel>  $expectedClass
     * @param  Closure(): (TModel|null)  $callback
     * @return TModel|null
     */
    public function rememberTeamModel(
        int|string $teamId,
        string $scope,
        array $context,
        int $ttl,
        string $expectedClass,
        Closure $callback,
    ): ?Model {
        if (! $this->enabled()) {
            return $callback();
        }

        $snapshot = $this->rememberTeamValue(
            $teamId,
            $scope,
            $context,
            $ttl,
            fn (): array => $this->snapshots->encodeModel($callback()),
        );

        try {
            return $this->snapshots->decodeModel((array) $snapshot, $expectedClass);
        } catch (Throwable) {
            $this->forgetDataKey($teamId, null, $scope, $context);
            $model = $callback();
            $this->putValue($teamId, null, $scope, $context, $ttl, $this->snapshots->encodeModel($model));

            return $model;
        }
    }

    public function invalidateTeam(int|string|null $teamId): void
    {
        if (! $this->validTeamId($teamId)) {
            return;
        }

        $this->store()->forever($this->teamVersionKey($teamId), Str::random(24));
        $this->clearRequestMemo();
    }

    public function invalidateSection(int|string|null $teamId, ?string $sectionUuid): void
    {
        if (! $this->validTeamId($teamId) || ! is_string($sectionUuid) || trim($sectionUuid) === '') {
            return;
        }

        $this->store()->forever($this->sectionVersionKey($teamId, $sectionUuid), Str::random(24));
        $this->clearRequestMemo();
    }

    /** @param array<string, mixed> $context */
    private function rememberValue(
        int|string $teamId,
        ?string $sectionUuid,
        string $scope,
        array $context,
        int $ttl,
        Closure $callback,
    ): mixed {
        if (! $this->enabled()) {
            return $callback();
        }

        $key = $this->dataKey($teamId, $sectionUuid, $scope, $context);
        $memo = $this->requestMemo();

        if (array_key_exists($key, $memo)) {
            return $memo[$key];
        }

        $store = $this->store();
        $cached = $store->get($key);

        if ($this->validPayload($cached)) {
            return $this->memoize($key, $cached['value']);
        }

        $compute = function () use ($store, $key, $ttl, $callback): array {
            $cached = $store->get($key);

            if ($this->validPayload($cached)) {
                return $cached;
            }

            $value = $callback();
            $this->assertCacheSafe($value);
            $payload = ['marker' => 'niva-section-cache', 'value' => $value];
            $store->put($key, $payload, max(1, $ttl));

            return $payload;
        };

        try {
            $lockProvider = $store->getStore();
            $payload = $lockProvider instanceof LockProvider
                ? $lockProvider
                    ->lock($key.':lock', max(1, (int) config('niva-template.public_section_cache.lock_seconds', 10)))
                    ->block(
                        max(1, (int) config('niva-template.public_section_cache.lock_wait_seconds', 3)),
                        $compute,
                    )
                : $compute();
        } catch (LockTimeoutException) {
            $value = $callback();
            $this->assertCacheSafe($value);
            $payload = ['marker' => 'niva-section-cache', 'value' => $value];
        }

        return $this->memoize($key, $payload['value']);
    }

    /** @param array<string, mixed> $context */
    private function putValue(
        int|string $teamId,
        ?string $sectionUuid,
        string $scope,
        array $context,
        int $ttl,
        mixed $value,
    ): void {
        $this->assertCacheSafe($value);
        $key = $this->dataKey($teamId, $sectionUuid, $scope, $context);
        $this->store()->put($key, ['marker' => 'niva-section-cache', 'value' => $value], max(1, $ttl));
        $this->memoize($key, $value);
    }

    /** @param array<string, mixed> $context */
    private function forgetDataKey(int|string $teamId, ?string $sectionUuid, string $scope, array $context): void
    {
        $key = $this->dataKey($teamId, $sectionUuid, $scope, $context);
        $this->store()->forget($key);
        $memo = $this->requestMemo();
        unset($memo[$key]);
        $this->setRequestMemo($memo);
    }

    /** @param array<string, mixed> $context */
    private function dataKey(int|string $teamId, ?string $sectionUuid, string $scope, array $context): string
    {
        $namespace = $this->normalizedNamespace();
        $schemaVersion = max(1, (int) config('niva-template.public_section_cache.schema_version', 1));
        $teamVersion = $this->version($this->teamVersionKey($teamId));
        $teamPart = $this->hashPart((string) $teamId);
        $sectionPart = 't';

        if ($sectionUuid !== null) {
            $sectionPart = 's:'.$this->hashPart($sectionUuid).':'.$this->version($this->sectionVersionKey($teamId, $sectionUuid));
        }

        $normalizedScope = $this->normalizedKeyPart($scope, 'data');
        $contextHash = substr(hash('sha256', json_encode($this->normalizeContext($context), JSON_THROW_ON_ERROR)), 0, 40);

        return implode(':', [
            $namespace,
            'v'.$schemaVersion,
            't',
            $teamPart,
            $teamVersion,
            $sectionPart,
            $normalizedScope,
            $contextHash,
        ]);
    }

    private function teamVersionKey(int|string $teamId): string
    {
        return $this->versionNamespace().':t:'.$this->hashPart((string) $teamId);
    }

    private function sectionVersionKey(int|string $teamId, string $sectionUuid): string
    {
        return $this->teamVersionKey($teamId).':s:'.$this->hashPart($sectionUuid);
    }

    private function versionNamespace(): string
    {
        $namespace = $this->normalizedNamespace();
        $schemaVersion = max(1, (int) config('niva-template.public_section_cache.schema_version', 1));

        return $namespace.':versions:v'.$schemaVersion;
    }

    private function version(string $key): string
    {
        $version = $this->store()->get($key);

        if (is_string($version) && $version !== '') {
            return $version;
        }

        $version = Str::random(24);
        $this->store()->forever($key, $version);

        return $version;
    }

    private function store(): Repository
    {
        $store = config('niva-template.public_section_cache.store');

        return Cache::store(is_string($store) && $store !== '' ? $store : null);
    }

    private function validPayload(mixed $payload): bool
    {
        return is_array($payload)
            && ($payload['marker'] ?? null) === 'niva-section-cache'
            && array_key_exists('value', $payload);
    }

    private function assertCacheSafe(mixed $value): void
    {
        if ($value === null || is_scalar($value)) {
            return;
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException('Public section cache values must contain only arrays and scalar values.');
        }

        foreach ($value as $item) {
            $this->assertCacheSafe($item);
        }
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function normalizeContext(array $context): array
    {
        ksort($context);

        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $context[$key] = array_is_list($value)
                    ? array_map(fn (mixed $item): mixed => is_array($item) ? $this->normalizeContext($item) : $item, $value)
                    : $this->normalizeContext($value);
            }
        }

        return $context;
    }

    private function memoize(string $key, mixed $value): mixed
    {
        $memo = $this->requestMemo();
        $memo[$key] = $value;
        $this->setRequestMemo($memo);

        return $value;
    }

    /** @return array<string, mixed> */
    private function requestMemo(): array
    {
        return app()->bound('request')
            ? (array) request()->attributes->get(self::REQUEST_MEMO_KEY, [])
            : [];
    }

    /** @param array<string, mixed> $memo */
    private function setRequestMemo(array $memo): void
    {
        if (app()->bound('request')) {
            request()->attributes->set(self::REQUEST_MEMO_KEY, $memo);
        }
    }

    private function clearRequestMemo(): void
    {
        $this->setRequestMemo([]);
    }

    private function validTeamId(int|string|null $teamId): bool
    {
        return is_int($teamId) || (is_string($teamId) && trim($teamId) !== '');
    }

    private function normalizedNamespace(): string
    {
        return $this->normalizedKeyPart(
            (string) config('niva-template.public_section_cache.namespace', 'niva-public-sections'),
            'niva-public-sections',
        );
    }

    private function normalizedKeyPart(string $value, string $fallback): string
    {
        $normalized = preg_replace('/[^A-Za-z0-9_.-]/', '-', $value) ?: $fallback;

        return strlen($normalized) <= 40
            ? $normalized
            : substr($normalized, 0, 20).'-'.substr(hash('sha256', $normalized), 0, 16);
    }

    private function hashPart(string $value): string
    {
        return substr(hash('sha256', $value), 0, 24);
    }
}
