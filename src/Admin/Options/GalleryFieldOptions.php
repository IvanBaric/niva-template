<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Admin\Options;

use Illuminate\Database\Eloquent\Model;
use IvanBaric\Gallery\Models\Gallery;
use IvanBaric\Gallery\Support\GalleryModels;
use IvanBaric\Pages\Admin\Contracts\FieldOptionsProvider;
use IvanBaric\Pages\Admin\Field;

final class GalleryFieldOptions implements FieldOptionsProvider
{
    /** @return array<int, array<string, mixed>> */
    public function options(Model $context, Field $field): array
    {
        $teamId = $context->getAttribute('team_id');
        $currentTeamId = corexis_tenant_id();

        if (! is_numeric($teamId) || ! is_numeric($currentTeamId) || (int) $teamId !== (int) $currentTeamId) {
            return [];
        }

        $galleryModel = GalleryModels::gallery();

        return $galleryModel::query()
            ->forTenant((int) $teamId)
            ->standalone()
            ->forCollection((string) config('gallery.default_collection', 'images'))
            ->orderBy('title')
            ->get(['uuid', 'title', 'description'])
            ->map(static fn (Gallery $gallery): array => [
                'value' => (string) $gallery->getAttribute('uuid'),
                'label' => $gallery->displayTitle(),
                'description' => (string) ($gallery->getAttribute('description') ?? ''),
                'group_key' => 'galleries',
                'group_label' => __('Dostupne galerije'),
                'group_description' => __('Odaberite albume koji će se prikazati u ovoj sekciji.'),
                'group_type' => 'gallery',
            ])
            ->all();
    }
}
