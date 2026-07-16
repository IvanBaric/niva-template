<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Admin\Options;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use IvanBaric\Pages\Admin\Contracts\FieldOptionsProvider;
use IvanBaric\Pages\Admin\Field;
use IvanBaric\Taxonomy\Support\TaxonomyModels;

final class ProductTaxonomyFieldOptions implements FieldOptionsProvider
{
    /** @return array<int, array<string, mixed>> */
    public function options(Model $context, Field $field): array
    {
        $teamId = $context->getAttribute('team_id');
        $currentTeamId = corexis_tenant_id();

        if (! is_numeric($teamId) || ! is_numeric($currentTeamId) || (int) $teamId !== (int) $currentTeamId) {
            return [];
        }

        $taxonomiesTable = TaxonomyModels::taxonomiesTable();
        $itemsTable = TaxonomyModels::taxonomyItemsTable();

        return DB::table($itemsTable)
            ->join($taxonomiesTable, $itemsTable.'.taxonomy_id', '=', $taxonomiesTable.'.id')
            ->where($itemsTable.'.team_id', (int) $teamId)
            ->where($taxonomiesTable.'.team_id', (int) $teamId)
            ->where($taxonomiesTable.'.type', 'like', 'product_%')
            ->orderBy($taxonomiesTable.'.name')
            ->orderBy($itemsTable.'.position')
            ->orderBy($itemsTable.'.name')
            ->get([
                $itemsTable.'.uuid as value',
                $itemsTable.'.name as label',
                $itemsTable.'.description',
                $taxonomiesTable.'.uuid as group_key',
                $taxonomiesTable.'.name as group_label',
                $taxonomiesTable.'.description as group_description',
                $taxonomiesTable.'.type as group_type',
            ])
            ->map(static fn (object $option): array => [
                'value' => (string) $option->value,
                'label' => (string) $option->label,
                'description' => (string) ($option->description ?? ''),
                'group_key' => (string) $option->group_key,
                'group_label' => (string) $option->group_label,
                'group_description' => (string) ($option->group_description ?? ''),
                'group_type' => (string) $option->group_type,
            ])
            ->all();
    }
}
