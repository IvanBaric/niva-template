<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Observers;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Database\Eloquent\Model;
use IvanBaric\Gallery\Models\Gallery;
use IvanBaric\Gallery\Support\GalleryModels;
use IvanBaric\NivaTemplate\Support\NivaTemplateModels;
use IvanBaric\NivaTemplate\Support\PublicSectionCache;
use IvanBaric\Pages\Models\Section;
use IvanBaric\Pages\Models\SectionItem;
use IvanBaric\Pages\Support\PagesModels;

final class PublicSectionCacheObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private readonly PublicSectionCache $cache) {}

    public function saved(Model $model): void
    {
        $this->invalidate($model);
    }

    public function deleted(Model $model): void
    {
        $this->invalidate($model);
    }

    public function restored(Model $model): void
    {
        $this->invalidate($model);
    }

    public function forceDeleted(Model $model): void
    {
        $this->invalidate($model);
    }

    public function invalidate(Model $model): void
    {
        $sectionClass = PagesModels::section();
        $sectionItemClass = PagesModels::sectionItem();
        $galleryClass = GalleryModels::gallery();
        $mediaClass = GalleryModels::media();

        if ($model instanceof $sectionClass) {
            $this->invalidateSection($model);

            return;
        }

        if ($model instanceof $sectionItemClass) {
            $section = $this->sectionForItem($model);

            if ($section !== null) {
                $this->invalidateSection($section);
            } else {
                $this->cache->invalidateTeam($this->teamId($model));
            }

            return;
        }

        if ($model instanceof $galleryClass) {
            $this->invalidateGallery($model);

            return;
        }

        if ($model instanceof $mediaClass) {
            $gallery = $this->galleryForMedia($model);

            if ($gallery !== null) {
                $this->invalidateGallery($gallery);
            }

            return;
        }

        $this->cache->invalidateTeam($this->teamId($model));
    }

    private function invalidateGallery(Gallery $gallery): void
    {
        $owner = $this->galleryOwner($gallery);

        if ($owner instanceof Section) {
            $this->invalidateSection($owner);

            return;
        }

        if ($owner instanceof SectionItem) {
            $section = $this->sectionForItem($owner);

            if ($section !== null) {
                $this->invalidateSection($section);

                return;
            }
        }

        $this->cache->invalidateTeam($this->teamId($gallery) ?? $this->teamId($owner));
    }

    private function invalidateSection(Model $section): void
    {
        $teamId = $this->teamId($section);

        if ($teamId === null && method_exists($section, 'page')) {
            $page = $section->relationLoaded('page')
                ? $section->getRelation('page')
                : $section->page()->withTrashed()->first();
            $teamId = $page instanceof Model ? $this->teamId($page) : null;
        }

        $this->cache->invalidateSection(
            $teamId,
            $this->stringAttribute($section, 'uuid'),
        );
    }

    private function sectionForItem(Model $item): ?Section
    {
        if ($item->relationLoaded('section')) {
            $section = $item->getRelation('section');

            return $section instanceof Section ? $section : null;
        }

        $sectionId = $item->getAttribute('section_id');

        if (! is_numeric($sectionId)) {
            return null;
        }

        $sectionClass = PagesModels::section();
        $query = $sectionClass::query();
        $query->withTrashed();

        $section = $query->find((int) $sectionId);

        return $section instanceof Section ? $section : null;
    }

    private function galleryOwner(Gallery $gallery): ?Model
    {
        if ($gallery->relationLoaded('galleryable')) {
            $owner = $gallery->getRelation('galleryable');

            return $owner instanceof Model ? $owner : null;
        }

        if ($gallery->getAttribute('galleryable_type') === null || $gallery->getAttribute('galleryable_id') === null) {
            return null;
        }

        $owner = $gallery->galleryable()->first();

        return $owner instanceof Model ? $owner : null;
    }

    private function galleryForMedia(Model $media): ?Gallery
    {
        if ($media->relationLoaded('model')) {
            $gallery = $media->getRelation('model');

            return $gallery instanceof Gallery ? $gallery : null;
        }

        $galleryId = $media->getAttribute('model_id');
        $galleryClass = GalleryModels::gallery();
        $galleryMorphClass = (new $galleryClass)->getMorphClass();

        if ($media->getAttribute('model_type') !== $galleryMorphClass || ! $this->validModelKey($galleryId)) {
            return null;
        }

        $gallery = $galleryClass::query()->find($galleryId);

        return $gallery instanceof Gallery ? $gallery : null;
    }

    private function teamId(?Model $model): int|string|null
    {
        if ($model === null) {
            return null;
        }

        $organizationClass = NivaTemplateModels::organization();
        $productClass = NivaTemplateModels::product();
        $column = match (true) {
            $organizationClass !== null && $model instanceof $organizationClass => (string) config('niva-template.organization.team_column', 'team_id'),
            $productClass !== null && $model instanceof $productClass => (string) config('niva-template.products.team_column', 'team_id'),
            default => (string) config('corexis.tenancy.id_column', 'team_id'),
        };

        return $model->getAttribute($column) ?? $model->getAttribute('team_id');
    }

    private function validModelKey(mixed $key): bool
    {
        return is_int($key) || (is_string($key) && trim($key) !== '');
    }

    private function stringAttribute(Model $model, string $attribute): ?string
    {
        $value = $model->getAttribute($attribute);

        return is_string($value) && trim($value) !== '' ? $value : null;
    }
}
