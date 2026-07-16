@php
    $contentBlocksLayout = (string) data_get($section, 'settings.layout_variant', 'mosaic');
    $contentBlocksLayout = $contentBlocksLayout !== '' ? $contentBlocksLayout : 'mosaic';

    $contentBlockLayouts = [
        'columns_2',
        'columns_3',
        'columns_4',
        'mosaic',
        'photo_cards',
        'editorial',
        'spotlight',
        'alternating',
        'path',
        'story_path',
        'studio',
        'media_right',
        'media_left',
        'cards',
        'showcase',
        'journal',
    ];

    if (! in_array($contentBlocksLayout, $contentBlockLayouts, true)) {
        throw new \InvalidArgumentException("Unsupported content_blocks layout [{$contentBlocksLayout}].");
    }

    $contentBlockColumnLayouts = ['columns_2', 'columns_3', 'columns_4'];
    $contentBlockFallbackIcons = ['sparkles', 'gift', 'users'];
    $contentBlockGridClass = match ($contentBlocksLayout) {
        'columns_2' => 'md:grid-cols-2',
        'columns_4' => 'sm:grid-cols-2 xl:grid-cols-4',
        default => 'md:grid-cols-2 xl:grid-cols-3',
    };
    $hasContentBlocksHeader = filled($eyebrow) || filled($title) || ($showSectionDescription && filled($description));
    $contentBlockImageUrl = function (mixed $item, string $conversion = 'large') use ($assetUrl): ?string {
        if (is_object($item) && method_exists($item, 'imageUrl')) {
            $url = $item->imageUrl($conversion);

            if (filled($url)) {
                return $url;
            }
        }

        return $assetUrl(data_get($item, 'image'));
    };
    $contentBlockText = function (mixed $item, bool $limit = false): ?string {
        $source = $item->localized('description') ?: $item->localized('content');

        if (! filled($source)) {
            return null;
        }

        $text = str((string) $source)->stripTags()->squish();

        return $limit ? $text->limit(150)->toString() : $text->toString();
    };
    $contentBlocksLegacyType = (string) data_get($section, 'settings.content_blocks_legacy_type', '');
    $contentBlockFallbackIcon = (string) data_get($section, 'settings.content_blocks_fallback_icon', match ($contentBlocksLegacyType) {
        'vision' => 'sun',
        'team' => 'users',
        'values' => 'heart',
        default => 'sparkles',
    });
    $contentBlockFallbackIcon = $contentBlockFallbackIcon !== '' ? $contentBlockFallbackIcon : 'sparkles';
@endphp

@if (in_array($contentBlocksLayout, $contentBlockColumnLayouts, true))
    <div @class([
        'cx-public-section-content' => $hasContentBlocksHeader,
    ])>
        <div class="cx-public-grid-compact {{ $contentBlockGridClass }}">
            @foreach ($items as $item)
                @php
                    $valueImage = $contentBlockImageUrl($item);
                    $valueText = $contentBlockText($item);
                    $valueIcon = filled($item->icon) ? (string) $item->icon : $contentBlockFallbackIcons[($loop->iteration - 1) % count($contentBlockFallbackIcons)];
                @endphp

                <article class="flex h-full min-h-48 flex-col rounded-xl bg-white p-5 shadow-sm shadow-zinc-950/5 ring-1 ring-zinc-200/70 dark:bg-zinc-950 dark:shadow-black/20 dark:ring-zinc-800">
                    <div class="flex size-[4.5rem] shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                        @if ($valueImage)
                            <img src="{{ $valueImage }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <x-corexis::public-image-placeholder class="h-full w-full" :icon="$valueIcon" icon-class="size-8" />
                        @endif
                    </div>

                    <div class="mt-5 min-w-0">
                        <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                        @if ($valueText)
                            <p class="mt-2 cx-public-body-compact text-zinc-600 dark:text-zinc-300">{{ $valueText }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@elseif ($contentBlocksLayout === 'editorial')
    <div class="cx-public-section-content-spacious cx-public-stack-showcase">
        @foreach ($items as $item)
            @php
                $featureImage = $contentBlockImageUrl($item, 'xlarge');
                $featureText = $contentBlockText($item);
            @endphp

            <article>
                <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                    @if ($featureImage)
                        <img src="{{ $featureImage }}" alt="" class="aspect-[16/5] w-full object-cover sm:aspect-[18/5]" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[16/5] w-full sm:aspect-[18/5]" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-9" />
                    @endif
                </figure>

                <div class="mt-6 grid gap-4 sm:grid-cols-[minmax(0,0.42fr)_minmax(0,0.58fr)] sm:items-start sm:gap-8">
                    <div class="min-w-0">
                        <div class="flex items-center gap-3">
                            @if ($item->icon)
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon :name="$item->icon" class="size-5" />
                                </span>
                            @endif
                            <h3 class="cx-public-featured-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                        </div>
                    </div>

                    @if ($featureText)
                        <p class="cx-public-body text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'spotlight')
    @php
        $spotlightItem = $items->first();
        $spotlightRest = $items->slice(1)->values();
    @endphp

    @if ($spotlightItem)
        @php
            $spotlightImage = $contentBlockImageUrl($spotlightItem, 'xlarge');
            $spotlightText = $contentBlockText($spotlightItem);
        @endphp

        <div class="cx-public-section-content-spacious">
            <article class="grid gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(18rem,0.72fr)] lg:items-center lg:gap-12">
                <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                    @if ($spotlightImage)
                        <img src="{{ $spotlightImage }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[16/9] w-full" :icon="$spotlightItem->icon ?: $contentBlockFallbackIcon" icon-class="size-10" />
                    @endif
                </figure>

                <div>
                    <div class="flex items-center gap-3">
                        @if ($spotlightItem->icon)
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                <flux:icon :name="$spotlightItem->icon" class="size-5" />
                            </span>
                        @endif
                        <h3 class="cx-public-featured-title text-zinc-950 dark:text-white">{{ $spotlightItem->localized('title') }}</h3>
                    </div>
                    @if ($spotlightText)
                        <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $spotlightText }}</p>
                    @endif
                </div>
            </article>

            @if ($spotlightRest->isNotEmpty())
                <div class="cx-public-section-content">
                    <div class="grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-5">
                        @foreach ($spotlightRest as $item)
                            @php
                                $featureImage = $contentBlockImageUrl($item, 'large');
                                $featureText = $contentBlockText($item);
                            @endphp

                            <article>
                                <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                    @if ($featureImage)
                                        <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-7" />
                                    @endif
                                </figure>

                                <div class="mt-5 flex items-center gap-3">
                                    @if ($item->icon)
                                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                            <flux:icon :name="$item->icon" class="size-4" />
                                        </span>
                                    @endif
                                    <h3 class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                </div>
                                @if ($featureText)
                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
@elseif ($contentBlocksLayout === 'alternating')
    <div class="cx-public-section-content-spacious cx-public-stack-editorial">
        @foreach ($items as $item)
            @php
                $featureImage = $contentBlockImageUrl($item, 'xlarge');
                $featureText = $contentBlockText($item);
            @endphp

            <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,0.9fr)] lg:items-center lg:gap-10">

                <figure @class([
                    'overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900',
                    'lg:order-2' => $loop->even,
                ])>
                    @if ($featureImage)
                        <img src="{{ $featureImage }}" alt="" class="aspect-[16/6] w-full object-cover lg:aspect-[18/5]" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[16/6] w-full lg:aspect-[18/5]" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-8" />
                    @endif
                </figure>

                <div @class([
                    'cx-public-copy-compact',
                    'lg:order-1' => $loop->even,
                ])>
                    <div class="flex items-center gap-3">
                        @if ($item->icon)
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                <flux:icon :name="$item->icon" class="size-5" />
                            </span>
                        @endif
                        <h3 class="cx-public-featured-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    </div>
                    @if ($featureText)
                        <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'path')
    <div class="cx-public-section-content-spacious cx-public-stack-editorial">
        @foreach ($items as $item)
            @php
                $featureImage = $contentBlockImageUrl($item, 'large');
                $featureText = $contentBlockText($item);
            @endphp
            <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1fr)] lg:items-center">
                <figure @class([
                    'overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/20',
                    'lg:order-2' => $loop->even,
                ])>
                    @if ($featureImage)
                        <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-10" />
                    @endif
                </figure>

                <div @class([
                    'cx-public-copy-compact',
                    'lg:pl-8' => $loop->odd,
                    'lg:justify-self-end lg:pr-8 lg:text-right' => $loop->even,
                ])>
                    @if ($item->icon)
                        <span @class([
                            'mb-5 inline-flex size-11 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] shadow-sm dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]',
                            'lg:ml-auto' => $loop->even,
                        ])>
                            <flux:icon :name="$item->icon" class="size-5" />
                        </span>
                    @endif
                    <h3 class="cx-public-featured-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    @if ($featureText)
                        <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'photo_cards')
    <div class="cx-public-section-content cx-public-grid-loose sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($items as $item)
            @php
                $featureImage = $contentBlockImageUrl($item, 'large');
                $featureText = $contentBlockText($item, true);
            @endphp

            <article class="group flex h-full flex-col overflow-hidden cx-public-surface-plain cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                <figure class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                    @if ($featureImage)
                        <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-9" />
                    @endif
                </figure>

                <div class="relative flex flex-1 flex-col px-5 pb-6 pt-8">
                    <span class="absolute -top-7 left-5 inline-flex size-14 items-center justify-center rounded-xl bg-[color:var(--niva-primary-700)] text-white shadow-md shadow-zinc-950/15 ring-4 ring-white dark:ring-zinc-950">
                        <flux:icon :name="$item->icon ?: $contentBlockFallbackIcon" class="size-6" />
                    </span>

                    <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    @if ($featureText)
                        <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'studio')
    <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
        <div class="cx-public-grid md:grid-cols-2 lg:grid-cols-3">
            @foreach ($items as $item)
                @php
                    $featureImage = $contentBlockImageUrl($item, 'large');
                    $featureText = $contentBlockText($item, true);
                    $tiltClass = match ($loop->iteration % 3) {
                        1 => 'lg:-rotate-1',
                        2 => 'lg:rotate-1',
                        default => 'lg:rotate-0',
                    };
                @endphp
                <article class="{{ $tiltClass }} group cx-public-surface-plain p-3 cx-public-card-hover hover:rotate-0 dark:bg-zinc-950 dark:shadow-black/20">
                    <div class="relative overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                        @if ($featureImage)
                            <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                        @else
                            <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-9" />
                        @endif

                        @if ($item->icon)
                            <span class="absolute right-3 top-3 inline-flex size-9 items-center justify-center rounded-full bg-white/95 text-[color:var(--niva-primary-700)] shadow-sm dark:bg-zinc-950/90 dark:text-[color:var(--niva-primary-300)]">
                                <flux:icon :name="$item->icon" class="size-4" />
                            </span>
                        @endif
                    </div>

                    <div class="px-2 pb-2 pt-4">
                        <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                        @if ($featureText)
                            <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@elseif ($contentBlocksLayout === 'cards')
    <div class="cx-public-section-content cx-public-grid-compact md:grid-cols-2 xl:grid-cols-3">
        @foreach ($items as $item)
            @php
                $itemImage = $contentBlockImageUrl($item);
                $itemText = $contentBlockText($item);
            @endphp
            <article class="flex h-full flex-col overflow-hidden cx-public-surface">
                @if ($itemImage)
                    <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                @else
                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-7" />
                @endif

                <div class="flex flex-1 flex-col p-5">
                    @if ($item->icon)
                        <span class="mb-4 inline-flex size-10 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] shadow-sm dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                            <flux:icon :name="$item->icon" class="size-5" />
                        </span>
                    @endif
                    @if ($item->localized('subtitle'))
                        <p class="mb-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->localized('subtitle') }}</p>
                    @endif
                    <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    @if ($itemText)
                        <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $itemText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'showcase')
    @php
        $featuredStoryItem = $items->first();
        $secondaryStoryItems = $items->slice(1);
    @endphp

    <div class="cx-public-section-content cx-public-grid lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
        @if ($featuredStoryItem)
            @php
                $itemImage = $contentBlockImageUrl($featuredStoryItem);
                $itemText = $contentBlockText($featuredStoryItem);
            @endphp
            <article class="overflow-hidden cx-public-surface">
                @if ($itemImage)
                    <img src="{{ $itemImage }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" decoding="async">
                @else
                    <x-corexis::public-image-placeholder class="aspect-[16/10]" :icon="$featuredStoryItem->icon ?: $contentBlockFallbackIcon" icon-class="size-10" />
                @endif
                <div class="p-6 sm:p-7">
                    @if ($featuredStoryItem->icon)
                        <span class="mb-4 inline-flex size-11 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] shadow-sm dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                            <flux:icon :name="$featuredStoryItem->icon" class="size-5" />
                        </span>
                    @endif
                    @if ($featuredStoryItem->localized('subtitle'))
                        <p class="mb-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $featuredStoryItem->localized('subtitle') }}</p>
                    @endif
                    <h3 class="cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">{{ $featuredStoryItem->localized('title') }}</h3>
                    @if ($itemText)
                        <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $itemText }}</p>
                    @endif
                </div>
            </article>
        @endif

        <div class="cx-public-stack">
            @foreach ($secondaryStoryItems as $item)
                @php
                    $itemImage = $contentBlockImageUrl($item);
                    $itemText = $contentBlockText($item);
                @endphp
                <article class="cx-public-grid-compact cx-public-surface cx-public-card-padding-compact sm:grid-cols-[8rem_1fr] sm:items-center">
                    @if ($itemImage)
                        <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full rounded-xl object-cover sm:h-28" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full rounded-xl sm:h-28" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-6" />
                    @endif
                    <div>
                        @if ($item->localized('subtitle'))
                            <p class="cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->localized('subtitle') }}</p>
                        @endif
                        <h3 class="mt-1 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                        @if ($itemText)
                            <p class="mt-2 line-clamp-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $itemText }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@elseif ($contentBlocksLayout === 'journal')
    <div class="cx-public-section-content cx-public-stack">
        @foreach ($items as $item)
            @php
                $itemImage = $contentBlockImageUrl($item);
                $itemText = $contentBlockText($item);
            @endphp
            <article class="cx-public-grid cx-public-surface cx-public-card-padding-compact sm:grid-cols-[11rem_1fr] sm:items-center">
                @if ($itemImage)
                    <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full rounded-xl object-cover sm:h-36" loading="lazy" decoding="async">
                @else
                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full rounded-xl sm:h-36" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-7" />
                @endif
                <div>
                    @if ($item->icon)
                        <span class="mb-4 inline-flex size-10 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] shadow-sm dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                            <flux:icon :name="$item->icon" class="size-5" />
                        </span>
                    @endif
                    @if ($item->localized('subtitle'))
                        <p class="cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->localized('subtitle') }}</p>
                    @endif
                    <h3 class="mt-1 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    @if ($itemText)
                        <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $itemText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'story_path' || in_array($contentBlocksLayout, ['media_right', 'media_left'], true))
    <div class="cx-public-section-content-spacious cx-public-stack-editorial">
        @foreach ($items as $item)
            @php
                $itemImage = $contentBlockImageUrl($item);
                $itemText = $contentBlockText($item);
                $imageFirst = $contentBlocksLayout === 'media_left' || ($contentBlocksLayout === 'story_path' && $loop->odd);
            @endphp
            <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1fr)] lg:items-center">
                <figure @class([
                    'overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/20',
                    'lg:order-2' => ! $imageFirst,
                ])>
                    @if ($itemImage)
                        <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-10" />
                    @endif
                </figure>

                <div @class([
                    'cx-public-copy-compact',
                    'lg:pl-8' => $imageFirst,
                    'lg:justify-self-end lg:pr-8 lg:text-right' => ! $imageFirst,
                ])>
                    @if ($item->icon)
                        <span @class([
                            'mb-5 inline-flex size-11 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] shadow-sm dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]',
                            'lg:ml-auto' => ! $imageFirst,
                        ])>
                            <flux:icon :name="$item->icon" class="size-5" />
                        </span>
                    @endif
                    @if ($item->localized('subtitle'))
                        <p class="mb-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->localized('subtitle') }}</p>
                    @endif
                    <h3 class="cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                    @if ($itemText)
                        <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $itemText }}</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@elseif ($contentBlocksLayout === 'mosaic')
    <div class="cx-public-section-content cx-public-grid-loose md:grid-cols-2 lg:grid-cols-3">
        @foreach ($items as $item)
            @php
                $featureImage = $contentBlockImageUrl($item, 'large');
                $featureText = $contentBlockText($item);
            @endphp
            <article class="group flex h-full flex-col overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                    @if ($featureImage)
                        <img src="{{ $featureImage }}" alt="" class="aspect-[16/9] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="aspect-[16/9] w-full" :icon="$item->icon ?: $contentBlockFallbackIcon" icon-class="size-10" />
                    @endif

                    <div class="absolute inset-x-0 bottom-0 p-4">
                        <div class="inline-flex max-w-[calc(100%-1rem)] items-center gap-2 rounded-full bg-white/95 px-3 py-2 text-zinc-950 shadow-sm shadow-zinc-950/10 backdrop-blur-sm dark:bg-zinc-950/95 dark:text-white dark:shadow-black/30">
                            @if ($item->icon)
                                <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon :name="$item->icon" class="size-3.5" />
                                </span>
                            @endif
                            <h3 class="min-w-0 cx-public-item-title-compact tracking-tight">{{ $item->localized('title') }}</h3>
                        </div>
                    </div>
                </div>

                @if ($featureText)
                    <div class="flex flex-1 items-start px-5 py-5">
                        <p class="cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                    </div>
                @endif
            </article>
        @endforeach
    </div>
@endif
