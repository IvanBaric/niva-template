<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Support;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class NivaTemplateModels
{
    /** @return class-string<Model>|null */
    public static function organization(): ?string
    {
        return self::model('organization');
    }

    /** @return class-string<Model>|null */
    public static function product(): ?string
    {
        return self::model('product');
    }

    public static function isProduct(mixed $value): bool
    {
        $model = self::product();

        return $model !== null && $value instanceof $model;
    }

    /** @return class-string<Model>|null */
    private static function model(string $key): ?string
    {
        $model = config("niva-template.models.{$key}");

        if ($model === null || $model === '') {
            return null;
        }

        if (! is_string($model) || ! is_a($model, Model::class, true)) {
            throw new InvalidArgumentException("Niva template model [{$key}] must extend ".Model::class.'.');
        }

        return $model;
    }
}
