@php
    $featuredValuesLayout = (string) data_get($section, 'settings.layout_variant', 'columns_3');
    $fallbackIcons = ['sparkles', 'gift', 'users'];
    $featuredValuesGridClass = match ($featuredValuesLayout) {
        'columns_2' => 'md:grid-cols-2',
        'columns_4' => 'sm:grid-cols-2 xl:grid-cols-4',
        default => 'md:grid-cols-2 xl:grid-cols-3',
    };
    $hasFeaturedValuesHeader = filled($eyebrow) || filled($title) || ($showSectionDescription && filled($description));
    $featuredValueImageUrl = function (mixed $item) use ($assetUrl): ?string {
        if (is_object($item) && method_exists($item, 'imageUrl')) {
            $url = $item->imageUrl('large');

            if (filled($url)) {
                return $url;
            }
        }

        return $assetUrl(data_get($item, 'image'));
    };
@endphp

@if ($type === 'featured_values')
    <div @class([
        'cx-public-section-content' => $hasFeaturedValuesHeader,
    ])>
        <div class="cx-public-grid-compact {{ $featuredValuesGridClass }}">
            @foreach ($items as $item)
                @php
                    $valueImage = $featuredValueImageUrl($item);
                    $valueTextSource = $item->localized('description') ?: $item->localized('content');
                    $valueText = filled($valueTextSource) ? str((string) $valueTextSource)->stripTags()->squish()->toString() : null;
                    $valueIcon = filled($item->icon) ? (string) $item->icon : $fallbackIcons[($loop->iteration - 1) % count($fallbackIcons)];
                @endphp

                <article class="flex h-full min-h-48 flex-col rounded-xl bg-white p-5 shadow-sm shadow-zinc-950/5 ring-1 ring-zinc-200/70 dark:bg-zinc-950 dark:shadow-black/20 dark:ring-zinc-800">
                    <div @class([
                        'flex size-[4.5rem] shrink-0 items-center justify-center overflow-hidden rounded-xl bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]',
                    ])>
                        @if ($valueImage)
                            <img src="{{ $valueImage }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async">
                        @else
                            <flux:icon :name="$valueIcon" class="size-8" />
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
@endif
