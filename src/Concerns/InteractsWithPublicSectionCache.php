<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use IvanBaric\NivaTemplate\Support\PublicSectionCache;

trait InteractsWithPublicSectionCache
{
    /**
     * @template TValue
     *
     * @param  array<string, mixed>  $context
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    private function rememberSectionValue(string $scope, array $context, bool $dynamic, Closure $callback): mixed
    {
        $teamId = $this->publicSectionCacheTeamId();
        $sectionUuid = $this->publicSectionCacheSectionUuid();

        if ($teamId === null || $sectionUuid === null) {
            return $callback();
        }

        $cache = app(PublicSectionCache::class);

        return $cache->rememberSectionValue(
            $teamId,
            $sectionUuid,
            $scope,
            $this->publicSectionCacheContext($context),
            $dynamic ? $cache->dynamicTtl() : $cache->staticTtl(),
            $callback,
        );
    }

    /**
     * @template TModel of Model
     *
     * @param  array<string, mixed>  $context
     * @param  class-string<TModel>  $expectedClass
     * @param  Closure(): EloquentCollection<int, TModel>  $callback
     * @return EloquentCollection<int, TModel>
     */
    private function rememberSectionModels(
        string $scope,
        string $expectedClass,
        array $context,
        bool $dynamic,
        Closure $callback,
    ): EloquentCollection {
        $teamId = $this->publicSectionCacheTeamId();
        $sectionUuid = $this->publicSectionCacheSectionUuid();

        if ($teamId === null || $sectionUuid === null) {
            return $callback();
        }

        $cache = app(PublicSectionCache::class);

        return $cache->rememberSectionModels(
            $teamId,
            $sectionUuid,
            $scope,
            $this->publicSectionCacheContext($context),
            $dynamic ? $cache->dynamicTtl() : $cache->staticTtl(),
            $expectedClass,
            $callback,
        );
    }

    /**
     * @template TValue
     *
     * @param  array<string, mixed>  $context
     * @param  Closure(): TValue  $callback
     * @return TValue
     */
    private function rememberTeamValue(string $scope, array $context, bool $dynamic, Closure $callback): mixed
    {
        $teamId = $this->publicSectionCacheTeamId();

        if ($teamId === null) {
            return $callback();
        }

        $cache = app(PublicSectionCache::class);

        return $cache->rememberTeamValue(
            $teamId,
            $scope,
            $this->publicSectionCacheContext($context),
            $dynamic ? $cache->dynamicTtl() : $cache->staticTtl(),
            $callback,
        );
    }

    /**
     * @template TModel of Model
     *
     * @param  class-string<TModel>  $expectedClass
     * @param  array<string, mixed>  $context
     * @param  Closure(): (TModel|null)  $callback
     * @return TModel|null
     */
    private function rememberTeamModel(
        string $scope,
        string $expectedClass,
        array $context,
        bool $dynamic,
        Closure $callback,
    ): ?Model {
        $teamId = $this->publicSectionCacheTeamId();

        if ($teamId === null) {
            return $callback();
        }

        $cache = app(PublicSectionCache::class);

        return $cache->rememberTeamModel(
            $teamId,
            $scope,
            $this->publicSectionCacheContext($context),
            $dynamic ? $cache->dynamicTtl() : $cache->staticTtl(),
            $expectedClass,
            $callback,
        );
    }

    private function publicSectionCacheTeamId(): int|string|null
    {
        $teamId = data_get($this, 'page.team_id') ?? data_get($this, 'section.team_id');

        if (is_int($teamId)) {
            return $teamId;
        }

        if (is_string($teamId) && trim($teamId) !== '') {
            return is_numeric($teamId) ? (int) $teamId : $teamId;
        }

        return null;
    }

    private function publicSectionCacheSectionUuid(): ?string
    {
        $uuid = data_get($this, 'section.uuid');

        return is_string($uuid) && trim($uuid) !== '' ? $uuid : null;
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function publicSectionCacheContext(array $context): array
    {
        return array_merge([
            'template' => (string) data_get($this, 'templateKey', ''),
            'section_type' => (string) data_get($this, 'section.type', ''),
            'locale' => app()->getLocale(),
        ], $context);
    }
}
