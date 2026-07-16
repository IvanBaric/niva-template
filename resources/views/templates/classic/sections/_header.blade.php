@php
    $hasPostCategoryFilters = isset($postCategoryFilters) && $postCategoryFilters->isNotEmpty();
    $activePostFilters = isset($activePostFilters) ? $activePostFilters : collect();
    $hasPostFilters = $hasPostCategoryFilters || $activePostFilters->isNotEmpty();
    $postFilterLoadingTarget = 'selectPostCategoryFilter,clearPostCategoryFilter,clearPostTagFilter,clearPostFilters';
    $sectionEditUser = auth()->user();
    $sectionEditTeamId = data_get($section, 'team_id');
    $canShowSectionEditLink = $sectionEditUser
        && filled(data_get($section, 'uuid'))
        && is_numeric($sectionEditTeamId)
        && is_numeric(data_get($sectionEditUser, 'current_team_id'))
        && (int) $sectionEditTeamId === (int) data_get($sectionEditUser, 'current_team_id')
        && \Illuminate\Support\Facades\Route::has('admin.sections.show');
    $canCycleSectionDesign = $canShowSectionEditLink
        && method_exists($this, 'canCycleSectionLayoutVariant')
        && $this->canCycleSectionLayoutVariant();
    $hasHeaderActions = $hasPostFilters || $canShowSectionEditLink || $canCycleSectionDesign;
    $sectionActionButtonBaseClass = 'inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-full transition duration-200 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary-200)] focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60 dark:focus:ring-[color:var(--niva-primary-700)]';
    $sectionActionButtonToneClass = $sectionTone === 'dark'
        ? 'text-white/75 ring-1 ring-white/15 hover:bg-white/10 hover:text-white'
        : 'bg-white/80 text-zinc-500 ring-1 ring-zinc-950/10 shadow-sm shadow-zinc-950/5 hover:bg-[color:var(--niva-primary-50)] hover:text-[color:var(--niva-primary-800)] hover:ring-[color:var(--niva-primary-200)] dark:bg-zinc-950/70 dark:text-zinc-300 dark:ring-white/10 dark:hover:bg-zinc-900 dark:hover:text-[color:var(--niva-primary-200)]';
    $sectionHeaderClass = $sectionHeaderClass ?? 'cx-public-section-header';
    $sectionHeaderCopyClass = $sectionHeaderCopyClass ?? 'cx-public-section-header-copy';
    $sectionHeaderActionsClass = $sectionHeaderActionsClass ?? 'cx-public-section-header-actions';
    $sectionHeaderButtonClass = $sectionHeaderButtonClass ?? ($sectionTone === 'dark' ? 'mt-7 cx-public-button-inverse' : 'mt-6 cx-public-button-primary');
    $sectionTitleAccentParts = is_array($sectionTitleAccentParts ?? null) ? $sectionTitleAccentParts : null;
    $sectionTitleAccentClass = $sectionTitleAccentClass ?? '';
@endphp

<div class="{{ $sectionHeaderClass }}">
    <div class="{{ $sectionHeaderCopyClass }}">
        @if ($eyebrow)
            <p class="{{ $eyebrowClass }}">{{ $eyebrow }}</p>
        @endif

        @if ($title)
            <h2 class="{{ $titleClass }}">
                @if ($sectionTitleAccentParts && filled($sectionTitleAccentParts['accent'] ?? null))
                    @if (filled($sectionTitleAccentParts['before'] ?? null))
                        {{ $sectionTitleAccentParts['before'] }} <span class="{{ $sectionTitleAccentClass }}">{{ $sectionTitleAccentParts['accent'] }}</span>
                    @else
                        <span class="{{ $sectionTitleAccentClass }}">{{ $sectionTitleAccentParts['accent'] }}</span>
                    @endif
                @else
                    {{ $title }}
                @endif
            </h2>

        @endif

        @if ($showSectionDescription && ($description || $type === 'features' || $type === 'partners'))
            <p class="{{ $descriptionClass }}">
                @if ($description)
                    {{ $description }}
                @elseif ($type === 'features')
                    {{ __('Sve što posjetitelj treba brzo razumjeti o radu udruge, aktivnostima i sadržaju koji može pronaći na stranici.') }}
                @else
                    {{ __('Suradnici i ustanove koje podržavaju aktivnosti, radionice i programe zajednice.') }}
                @endif
            </p>
        @endif

        @if ($buttonText && $buttonUrl && in_array($type, ['featured_products', 'all_products', 'featured_news', 'latest_news', 'taxonomy_news'], true))
            <a href="{{ $buttonUrl }}" class="{{ $sectionHeaderButtonClass }}">
                {{ $buttonText }}
            </a>
        @endif
    </div>

    @if ($hasHeaderActions)
        <div class="{{ $sectionHeaderActionsClass }}">
            @if ($hasPostFilters)
                @foreach ($activePostFilters as $activeFilter)
                    @php
                        $clearFilterAction = $activeFilter['query_key'] === 'oznaka' ? 'clearPostTagFilter' : 'clearPostCategoryFilter';
                    @endphp

                    <span wire:loading.class="opacity-0 scale-95" wire:target="{{ $clearFilterAction }},clearPostFilters" class="origin-center cx-public-badge transition duration-200 ease-out">
                        <span class="truncate">{{ $activeFilter['label'] }}: {{ $activeFilter['name'] }}</span>
                        <button type="button" wire:click="{{ $clearFilterAction }}" wire:loading.attr="disabled" wire:target="{{ $postFilterLoadingTarget }}" class="cx-public-badge-close" aria-label="{{ __('Ukloni filter') }}: {{ $activeFilter['name'] }}">
                            <flux:icon name="x-mark" class="size-3.5" />
                        </button>
                    </span>
                @endforeach

                <flux:dropdown position="bottom" align="end">
                    <flux:button type="button" variant="ghost" icon="adjustments-horizontal" class="cx-public-icon-button" :aria-label="__('Filtriraj objave')" />

                    <flux:menu class="min-w-64">
                        @if ($hasActivePostFilter ?? false)
                            <flux:menu.item as="button" type="button" wire:click="clearPostFilters" wire:loading.attr="disabled" wire:target="{{ $postFilterLoadingTarget }}" icon="x-mark" class="cursor-pointer disabled:cursor-wait">
                                {{ __('Sve objave') }}
                            </flux:menu.item>
                            <flux:menu.separator />
                        @endif

                        @if ($hasPostCategoryFilters)
                            <flux:menu.item disabled>{{ __('Kategorije') }}</flux:menu.item>

                            @foreach ($postCategoryFilters as $filter)
                                @if ($filter['active'])
                                    <flux:menu.item disabled icon="check">
                                        <span class="font-semibold">
                                            {{ $filter['name'] }} ({{ $filter['count'] }})
                                        </span>
                                        <span class="sr-only">{{ __('odabrano') }}</span>
                                    </flux:menu.item>
                                @else
                                    <flux:menu.item as="button" type="button" wire:click="selectPostCategoryFilter('{{ $filter['slug'] }}')" wire:loading.attr="disabled" wire:target="{{ $postFilterLoadingTarget }}" class="cursor-pointer disabled:cursor-wait">
                                        {{ $filter['name'] }} ({{ $filter['count'] }})
                                    </flux:menu.item>
                                @endif
                            @endforeach
                        @endif
                    </flux:menu>
                </flux:dropdown>
            @endif

            @if ($canShowSectionEditLink)
                <x-public-section-edit-link :section="$section" :tone="$sectionTone" />
            @endif

            {{-- Privremeno skriveno radi testiranja sučelja bez izmjene izgleda pojedine sekcije.
            @if ($canCycleSectionDesign)
                <flux:tooltip :content="__('Prethodni izgled sekcije: :layout', ['layout' => $this->previousSectionLayoutVariantLabel()])" position="bottom">
                    <button
                        type="button"
                        wire:click="cycleSectionLayoutVariant('previous')"
                        wire:loading.attr="disabled"
                        wire:target="cycleSectionLayoutVariant"
                        class="{{ $sectionActionButtonBaseClass }} {{ $sectionActionButtonToneClass }}"
                        aria-label="{{ __('Prethodni izgled sekcije') }}"
                        title="{{ __('Prethodni izgled sekcije') }}"
                    >
                        <flux:icon name="chevron-left" class="size-4" wire:loading.remove wire:target="cycleSectionLayoutVariant" />
                        <flux:icon.loading class="size-4" wire:loading wire:target="cycleSectionLayoutVariant" />
                        <span class="sr-only">{{ __('Prethodni izgled sekcije') }}</span>
                    </button>
                </flux:tooltip>

                <flux:tooltip :content="__('Sljedeći izgled sekcije: :layout', ['layout' => $this->nextSectionLayoutVariantLabel()])" position="bottom">
                    <button
                        type="button"
                        wire:click="cycleSectionLayoutVariant('next')"
                        wire:loading.attr="disabled"
                        wire:target="cycleSectionLayoutVariant"
                        class="{{ $sectionActionButtonBaseClass }} {{ $sectionActionButtonToneClass }}"
                        aria-label="{{ __('Sljedeći izgled sekcije') }}"
                        title="{{ __('Sljedeći izgled sekcije') }}"
                    >
                        <flux:icon name="chevron-right" class="size-4" wire:loading.remove wire:target="cycleSectionLayoutVariant" />
                        <flux:icon.loading class="size-4" wire:loading wire:target="cycleSectionLayoutVariant" />
                        <span class="sr-only">{{ __('Sljedeći izgled sekcije') }}</span>
                    </button>
                </flux:tooltip>
            @endif
            --}}
        </div>
    @endif
</div>
