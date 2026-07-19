@php
    $type = (string) data_get($section, 'type');
    $items = $this->sectionItems();
    $showSectionTitle = (bool) data_get($section, 'settings.show_title', true);
    $showSectionDescription = (bool) data_get($section, 'settings.show_description', true);
    $eyebrow = $this->value('eyebrow');
    $title = $showSectionTitle ? $this->value('title', $section?->localized('title')) : null;
    $description = $showSectionDescription ? $this->value('description', $section?->localized('description') ?: $section?->localized('content')) : null;
    $image = $this->value('image', data_get($section, 'image'));
    $buttonText = $this->value('button_text', $section?->localized('button_text'));
    $publicUrl = app(\IvanBaric\Corexis\Support\PublicUrl::class);
    $buttonUrl = $publicUrl->sanitize($this->value('button_url', data_get($section, 'button_url')));
    $loadMoreLabel = trim((string) data_get($section, 'settings.load_more_label', ''));
    $loadMoreLabel = $loadMoreLabel !== '' ? $loadMoreLabel : __('Prikaži više');
    $records = in_array($type, ['featured_products', 'all_products', 'featured_news', 'latest_news', 'taxonomy_news', 'gallery', 'gallery_grid'], true)
        ? $this->records()
        : collect();
    $emptyStatePreviewEnabled = $this->emptyStatePreviewEnabled();
    $postFiltersEnabled = $this->usesPostFilters();
    $postCategoryFilters = $postFiltersEnabled
        ? $this->postCategoryFilters()
        : collect();
    $hasActivePostFilter = $postFiltersEnabled && $this->hasActivePostFilter();
    $activePostFilters = $postFiltersEnabled
        ? $this->activePostFilters()
        : collect();

    $assetUrl = function ($path) {
        if (! is_string($path) || $path === '') {
            return null;
        }

        return corexis_public_media_url($path);
    };

    $storyTypes = ['mission', 'vision', 'values', 'team'];
    $isStorySection = in_array($type, $storyTypes, true);
    $contentBlocksLegacyType = $type === 'content_blocks' ? (string) data_get($section, 'settings.content_blocks_legacy_type', '') : '';
    $sectionClassType = $contentBlocksLegacyType !== '' ? $contentBlocksLegacyType : $type;

    $sectionTone = match ($type) {
        'statistics', 'contact', 'partners', 'faq', 'how_to_order' => 'muted',
        default => 'light',
    };

    $sectionClass = $sectionClassType === 'featured_values'
        ? 'cx-public-section-bg px-6 py-10'
        : (in_array($sectionClassType, $storyTypes, true)
        ? match ($sectionClassType) {
            'mission' => 'cx-public-section-bg-warm px-6 pb-8 pt-16',
            'team' => 'cx-public-section-bg-warm px-6 pb-20 pt-8',
            default => 'cx-public-section-bg-warm px-6 py-8',
        }
        : match ($sectionTone) {
            'dark' => 'cx-public-section-bg-inverse px-6 py-20',
            'muted' => 'cx-public-section-bg-muted px-6 py-20',
            default => 'cx-public-section-bg px-6 py-20',
        });

    $eyebrowClass = $sectionTone === 'dark'
        ? 'cx-public-eyebrow text-[color:var(--niva-primary-300)]'
        : 'cx-public-eyebrow text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]';
    $titleClass = $sectionTone === 'dark'
        ? 'mt-2 cx-public-section-title tracking-tight text-white'
        : 'mt-2 cx-public-section-title tracking-tight text-zinc-950 dark:text-white';
    $descriptionClass = $sectionTone === 'dark'
        ? 'mt-4 cx-public-section-description text-zinc-300'
        : 'mt-4 cx-public-section-description text-zinc-600 dark:text-zinc-300';
    $sectionHeaderVariant = $this->sectionHeaderVariant();
    $sectionHeaderCentered = in_array($sectionHeaderVariant, ['center', 'center_accent', 'center_colored', 'marker'], true);
    $sectionHeaderSplit = in_array($sectionHeaderVariant, ['split', 'split_accent', 'split_colored'], true);
    $sectionHeaderSideLabel = $sectionHeaderVariant === 'side_label';
    $sectionHeaderAccent = in_array($sectionHeaderVariant, ['left_accent', 'center_accent', 'split_accent'], true);
    $sectionHeaderColored = in_array($sectionHeaderVariant, ['left_colored', 'center_colored', 'split_colored'], true);
    $sectionHeaderMarker = $sectionHeaderVariant === 'marker';
    $sectionHeaderClass = 'cx-public-section-header'
        .($sectionHeaderCentered ? ' text-center' : '');
    $sectionHeaderCopyClass = 'cx-public-section-header-copy'
        .($sectionHeaderCentered ? ' mx-auto max-w-3xl text-center' : '')
        .($sectionHeaderSplit ? ' max-w-none lg:grid lg:max-w-none lg:grid-cols-[minmax(0,0.95fr)_minmax(18rem,0.75fr)] lg:items-end lg:gap-x-10' : '')
        .($sectionHeaderSideLabel ? ' border-l-[3px] border-[color:var(--niva-primary)] pl-5 sm:pl-7 dark:border-[color:var(--niva-primary-300)]' : '');
    $sectionHeaderActionsClass = 'cx-public-section-header-actions'
        .($sectionHeaderCentered ? ' justify-center' : '')
        .($sectionHeaderSplit ? ' lg:pt-0' : '');
    $sectionHeaderButtonClass = $sectionTone === 'dark' ? 'mt-7 cx-public-button-inverse' : 'mt-6 cx-public-button-primary';
    $sectionTitleAccentParts = ($sectionHeaderColored || $sectionHeaderMarker) ? $this->sectionTitleAccentParts(is_string($title) ? $title : null) : null;
    $sectionTitleAccentClass = $sectionHeaderMarker
        ? ($sectionTone === 'dark'
            ? 'inline rounded-md bg-white/12 px-1.5 text-[color:var(--niva-primary-100)] ring-1 ring-white/15 box-decoration-clone'
            : 'inline rounded-md bg-[color:var(--niva-primary-100)] px-1.5 text-[color:var(--niva-primary-800)] ring-1 ring-[color:var(--niva-primary-200)] box-decoration-clone dark:bg-white/10 dark:text-[color:var(--niva-primary-200)] dark:ring-white/10')
        : ($sectionTone === 'dark'
            ? 'text-[color:var(--niva-primary-100)] drop-shadow-sm'
            : 'text-[color:var(--niva-primary-700)] drop-shadow-sm dark:text-[color:var(--niva-primary-200)]');
    if ($sectionHeaderCentered) {
        $titleClass .= ' mx-auto max-w-3xl [text-wrap:balance]';
        $descriptionClass .= ' mx-auto max-w-2xl [text-wrap:pretty]';
    }

    if ($sectionHeaderSplit) {
        $eyebrowClass .= ' lg:col-span-2';
        $titleClass .= ' lg:col-start-1 lg:row-start-2 [text-wrap:balance]';
        $descriptionClass .= ' lg:col-start-2 lg:row-start-2 lg:mt-0 [text-wrap:pretty]';
        $sectionHeaderButtonClass .= ' lg:col-start-2 lg:mt-5';
    }

    if ($sectionHeaderSideLabel) {
        $titleClass .= ' [text-wrap:balance]';
        $descriptionClass .= ' max-w-2xl [text-wrap:pretty]';
    }

    if ($sectionHeaderAccent) {
        $eyebrowClass .= $sectionTone === 'dark'
            ? ' inline-flex w-fit rounded-full bg-white/10 px-3 py-1 text-white ring-1 ring-white/15'
            : ' inline-flex w-fit rounded-full bg-[color:var(--niva-primary-50)] px-3 py-1 ring-1 ring-[color:var(--niva-primary-100)] dark:bg-white/5 dark:ring-white/10';
        $titleClass .= $sectionTone === 'dark'
            ? ' !text-[color:var(--niva-primary-100)] [text-wrap:balance] drop-shadow-sm'
            : ' !text-[color:var(--niva-primary-800)] [text-wrap:balance] drop-shadow-sm dark:!text-[color:var(--niva-primary-200)]';
        $descriptionClass .= $sectionTone === 'dark'
            ? ' max-w-2xl font-medium text-zinc-200 [text-wrap:pretty]'
            : ' max-w-2xl font-medium text-zinc-700 [text-wrap:pretty] dark:text-zinc-200';
    }

    if ($sectionHeaderColored || $sectionHeaderMarker) {
        $titleClass .= ' [text-wrap:balance]';
    }
    $hasSocialItemLinks = $type === 'social_links'
        && $items->contains(fn ($item) => filled($publicUrl->sanitize(data_get($item, 'url'))));
    $hasSharedSocialLinks = $type === 'social_links'
        && $this->socialLinks() !== [];
    $hasLegacySocialLinks = $type === 'social_links'
        && collect((array) data_get($section->settings, 'links', []))
            ->contains(fn ($url) => filled($publicUrl->sanitize($url)));
    $showSocialLinksEmptyState = $type === 'social_links'
        && ! $hasSharedSocialLinks
        && ! $hasSocialItemLinks
        && ! $hasLegacySocialLinks;
    $hasVideoLinks = $type === 'video'
        && $items->contains(function ($item): bool {
            $videoUrl = (string) ($item->setting('youtube_url') ?: data_get($item, 'url') ?: $item->setting('embed_url'));

            return \IvanBaric\Pages\Support\YouTubeVideo::fromUrl($videoUrl) !== null;
        });
    $showVideoEmptyState = $type === 'video' && ! $hasVideoLinks;
    $itemEmptyStateTypes = [
        'statistics',
        'features',
        'featured_values',
        'content_blocks',
        'collaboration',
        'partners',
        'faq',
        'testimonials',
        'how_to_order',
        'mission',
        'vision',
        'values',
        'team',
    ];
    $showItemEmptyState = in_array($type, $itemEmptyStateTypes, true) && $items->isEmpty();
    $calendarLayoutForHeader = (string) data_get($section, 'settings.layout_variant', 'calendar-split');
    $skipSectionHeader = $type === 'calendar' && $calendarLayoutForHeader === 'calendar-split';
    $shouldRenderSection = true;
    $showPreviewEmptyState = $this->shouldShowPreviewEmptyState($type, $items, $records);
    $previewEmptyStateMessage = $this->previewEmptyStateMessage($type);
    $showSectionEmptyState = $showSocialLinksEmptyState
        || $showVideoEmptyState
        || $showItemEmptyState
        || $showPreviewEmptyState;
    $emptyStateContent = match ($type) {
        'statistics' => ['icon' => 'chart-bar', 'title' => __('Podaci uskoro'), 'description' => __('Statistički podaci prikazat će se ovdje kada budu spremni za objavu.')],
        'features', 'featured_values', 'content_blocks' => ['icon' => 'squares-2x2', 'title' => __('Sadržaj uskoro'), 'description' => __('Sadržaj ove sekcije prikazat će se ovdje kada bude spreman za objavu.')],
        'collaboration' => ['icon' => 'hand-raised', 'title' => __('Informacije uskoro'), 'description' => __('Informacije o uključivanju i suradnji prikazat će se ovdje kada budu spremne.')],
        'partners' => ['icon' => 'building-office-2', 'title' => __('Partneri uskoro'), 'description' => __('Partneri će se prikazati ovdje kada budu spremni za objavu.')],
        'faq' => ['icon' => 'question-mark-circle', 'title' => __('Pitanja i odgovori uskoro'), 'description' => __('Odgovori na česta pitanja prikazat će se ovdje kada budu pripremljeni.')],
        'testimonials' => ['icon' => 'chat-bubble-left-right', 'title' => __('Dojmovi uskoro'), 'description' => __('Dojmovi i iskustva prikazat će se ovdje kada budu spremni za objavu.')],
        'how_to_order' => ['icon' => 'list-bullet', 'title' => __('Upute uskoro'), 'description' => __('Koraci i upute prikazat će se ovdje kada budu pripremljeni.')],
        'mission', 'vision', 'values', 'team' => ['icon' => 'sparkles', 'title' => __('Sadržaj uskoro'), 'description' => __('Sadržaj ove sekcije prikazat će se ovdje kada bude spreman za objavu.')],
        'social_links' => ['icon' => 'share', 'title' => __('Društvene mreže još nisu povezane.'), 'description' => __('Poveznice će se prikazati ovdje kada budu spremne za objavu.')],
        'video' => ['icon' => 'film', 'title' => __('Video još nije povezan.'), 'description' => __('Video će se prikazati ovdje kada poveznica bude spremna za objavu.')],
        default => ['icon' => 'sparkles', 'title' => __($previewEmptyStateMessage), 'description' => null],
    };
@endphp

<div>
@if ($shouldRenderSection)
    <section id="{{ $this->sectionAnchorId() }}" class="{{ $sectionClass }} scroll-mt-24" data-scroll-reveal>
    <div class="mx-auto cx-public-container">
        @unless ($skipSectionHeader)
            @include('niva-template::templates.classic.sections._header')
        @endunless

        @if ($showSectionEmptyState)
            <x-public-section-empty-state
                :section="$section"
                class="cx-public-section-content"
                :icon="$emptyStateContent['icon']"
                :title="$emptyStateContent['title']"
                :description="$emptyStateContent['description']"
                compact
            />
        @else
        @switch($type)
            @case('statistics')
                @include('niva-template::templates.classic.sections.statistics')
                @break

            @case('features')
                @include('niva-template::templates.classic.sections.features')
                @break

            @case('featured_values')
                @include('niva-template::templates.classic.sections.featured_values')
                @break

            @case('content_blocks')
                @include('niva-template::templates.classic.sections.content_blocks')
                @break

            @case('collaboration')
                @include('niva-template::templates.classic.sections.collaboration')
                @break

            @case('partners')
                @include('niva-template::templates.classic.sections.partners')
                @break

            @case('gallery')
            @case('gallery_grid')
                @if ($this->usesDirectGallery())
                    @include('niva-template::templates.classic.sections.photo_gallery')
                @else
                    @include('niva-template::templates.classic.sections.gallery')
                @endif
                @break

            @case('photo_gallery')
                @include('niva-template::templates.classic.sections.photo_gallery')
                @break

            @case('faq')
                @include('niva-template::templates.classic.sections.faq')
                @break

            @case('testimonials')
                @include('niva-template::templates.classic.sections.testimonials')
                @break

            @case('featured_products')
            @case('all_products')
            @case('featured_news')
            @case('latest_news')
            @case('taxonomy_news')
                @include('niva-template::templates.classic.sections.records')
                @break

            @case('how_to_order')
                @include('niva-template::templates.classic.sections.how_to_order')
                @break

            @case('about')
                @include('niva-template::templates.classic.sections.about')
                @break

            @case('mission')
            @case('vision')
            @case('values')
            @case('team')
                @include('niva-template::templates.classic.sections.story')
                @break

            @case('contact')
                @include('niva-template::templates.classic.sections.contact')
                @break

            @case('social_links')
                @include('niva-template::templates.classic.sections.social_links')
                @break

            @case('video')
                @include('niva-template::templates.classic.sections.video', [
                    'section' => $section,
                    'items' => $items,
                    'title' => $title,
                ])
                @break

            @case('calendar')
                @include('niva-template::templates.classic.sections.calendar')
                @break

            @default
                @include('niva-template::templates.classic.sections.fallback')
        @endswitch
        @endif

    </div>
    </section>
@endif
</div>
