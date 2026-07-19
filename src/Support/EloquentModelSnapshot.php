<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Support;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class EloquentModelSnapshot
{
    /** @return array<string, mixed> */
    public function encodeModel(?Model $model): array
    {
        if ($model === null) {
            return ['kind' => 'null'];
        }

        return [
            'kind' => 'model',
            'class' => $model::class,
            'connection' => $model->getConnectionName(),
            'attributes' => $this->encodeValue($model->getAttributes()),
            'relations' => $this->encodeValue($model->getRelations()),
        ];
    }

    /**
     * @template TModel of Model
     *
     * @param  EloquentCollection<int, TModel>  $models
     * @return array<string, mixed>
     */
    public function encodeCollection(EloquentCollection $models): array
    {
        return [
            'kind' => 'eloquent_collection',
            'models' => $models
                ->map(fn (Model $model): array => $this->encodeModel($model))
                ->values()
                ->all(),
        ];
    }

    /**
     * @template TModel of Model
     *
     * @param  array<string, mixed>  $snapshot
     * @param  class-string<TModel>  $expectedClass
     * @return TModel|null
     */
    public function decodeModel(array $snapshot, string $expectedClass): ?Model
    {
        if (($snapshot['kind'] ?? null) === 'null') {
            return null;
        }

        $model = $this->decodeValue($snapshot);

        if (! $model instanceof $expectedClass) {
            throw new InvalidArgumentException("Cached model must be an instance of [{$expectedClass}].");
        }

        return $model;
    }

    /**
     * @template TModel of Model
     *
     * @param  array<string, mixed>  $snapshot
     * @param  class-string<TModel>  $expectedClass
     * @return EloquentCollection<int, TModel>
     */
    public function decodeCollection(array $snapshot, string $expectedClass): EloquentCollection
    {
        if (($snapshot['kind'] ?? null) !== 'eloquent_collection' || ! is_array($snapshot['models'] ?? null)) {
            throw new InvalidArgumentException('Cached value is not an Eloquent collection snapshot.');
        }

        $models = [];

        foreach ($snapshot['models'] as $modelSnapshot) {
            $model = is_array($modelSnapshot) ? $this->decodeValue($modelSnapshot) : null;

            if (! $model instanceof $expectedClass) {
                throw new InvalidArgumentException("Cached collection contains an unexpected model; expected [{$expectedClass}].");
            }

            $models[] = $model;
        }

        return new EloquentCollection($models);
    }

    private function encodeValue(mixed $value): mixed
    {
        if ($value instanceof Model) {
            return $this->encodeModel($value);
        }

        if ($value instanceof EloquentCollection) {
            return $this->encodeCollection($value);
        }

        if ($value instanceof Collection) {
            return [
                'kind' => 'support_collection',
                'items' => $this->encodeValue($value->all()),
            ];
        }

        if ($value instanceof DateTimeInterface) {
            return [
                'kind' => 'datetime',
                'value' => $value->format(DateTimeInterface::ATOM),
            ];
        }

        if (is_array($value)) {
            $encoded = [];

            foreach ($value as $key => $item) {
                $encoded[$key] = $this->encodeValue($item);
            }

            return $encoded;
        }

        if ($value === null || is_scalar($value)) {
            return $value;
        }

        throw new InvalidArgumentException('Unsupported value in Eloquent cache snapshot: '.get_debug_type($value));
    }

    private function decodeValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $kind = $value['kind'] ?? null;

        if ($kind === 'model') {
            $class = $value['class'] ?? null;

            if (! is_string($class) || ! is_a($class, Model::class, true)) {
                throw new InvalidArgumentException('Cached model class is invalid.');
            }

            $attributes = $this->decodeValue($value['attributes'] ?? []);
            $relations = $this->decodeValue($value['relations'] ?? []);

            if (! is_array($attributes) || ! is_array($relations)) {
                throw new InvalidArgumentException('Cached model attributes or relations are invalid.');
            }

            /** @var Model $prototype */
            $prototype = new $class;
            $connection = is_string($value['connection'] ?? null) ? $value['connection'] : null;
            $model = $prototype->newFromBuilder($attributes, $connection);

            foreach ($relations as $name => $relation) {
                if (is_string($name)) {
                    $model->setRelation($name, $relation);
                }
            }

            return $model;
        }

        if ($kind === 'eloquent_collection') {
            $models = $value['models'] ?? null;

            if (! is_array($models)) {
                throw new InvalidArgumentException('Cached Eloquent collection is invalid.');
            }

            $decoded = collect($models)->map(fn (mixed $model): mixed => $this->decodeValue($model))->values();
            $prototype = $decoded->first();

            return $prototype instanceof Model
                ? $prototype->newCollection($decoded->all())
                : new EloquentCollection;
        }

        if ($kind === 'support_collection') {
            $items = $this->decodeValue($value['items'] ?? []);

            return new Collection(is_array($items) ? $items : []);
        }

        if ($kind === 'datetime') {
            return new \DateTimeImmutable((string) ($value['value'] ?? 'now'));
        }

        $decoded = [];

        foreach ($value as $key => $item) {
            $decoded[$key] = $this->decodeValue($item);
        }

        return $decoded;
    }
}
