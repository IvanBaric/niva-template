        <?php if ($type === 'gallery' || $type === 'gallery_grid') { ?>
            @php
                $galleryLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $galleryLayout = in_array($galleryLayout, ['cards', 'text_cards', 'masonry', 'featured', 'wall', 'journal', 'carousel'], true) ? $galleryLayout : 'cards';
                $galleryCarouselName = 'gallery-carousel-'.(string) data_get($section, 'uuid', data_get($section, 'id', 'section'));
                $galleryCarouselHasMore = $galleryLayout === 'carousel' && $this->hasMoreCarouselItems($type);
                $galleryEntries = collect();

                if ($records->isNotEmpty()) {
                    foreach ($records as $gallery) {
                        if (is_array($gallery)) {
                            $galleryEntries->push($gallery);

                            continue;
                        }

                        $cover = $gallery->featuredOrFirstMedia();
                        $coverUrl = null;
                        $coverLargeUrl = null;

                        if ($cover) {
                            try {
                                if (method_exists($cover, 'getAvailableUrl')) {
                                    $coverUrl = $cover->getAvailableUrl(['thumb', 'large']);
                                    $coverLargeUrl = $cover->getAvailableUrl(['large', 'thumb']);
                                } else {
                                    $coverUrl = $cover->getUrl('thumb');
                                    $coverLargeUrl = $cover->getUrl('large');
                                }
                            } catch (\Throwable) {
                                $coverUrl = $cover->getUrl();
                                $coverLargeUrl = $coverUrl;
                            }
                        }

                        $mediaCount = (int) ($gallery->media_count ?? $gallery->getMedia($gallery->collection_name)->count());

                        $galleryEntries->push([
                            'title' => $gallery->displayTitle(),
                            'description' => $gallery->description,
                            'image' => $coverUrl,
                            'image_large' => $coverLargeUrl ?: $coverUrl,
                            'url' => $this->galleryContentUrl((string) ($gallery->slug ?: $gallery->uuid)),
                            'count' => $mediaCount,
                            'count_label' => trans_choice(':count fotografija|:count fotografije|:count fotografija', $mediaCount, ['count' => $mediaCount]),
                        ]);
                    }
                } else {
                    foreach ($items as $item) {
                        $galleryEntries->push([
                            'title' => $item->localized('title'),
                            'description' => $item->localized('description') ?: $item->localized('content'),
                            'image' => method_exists($item, 'imageUrl') ? $item->imageUrl('thumb') : $assetUrl(data_get($item, 'image')),
                            'url' => null,
                            'count' => null,
                            'count_label' => null,
                        ]);
                    }
                }
            @endphp

            @if ($galleryEntries->isEmpty())
                <x-public-section-empty-state
                    :section="$section"
                    class="cx-public-section-content"
                    icon="photo"
                    :title="__('Galerija je spremna za prve fotografije.')"
                    :description="__('Albumi i fotografije prikazat će se ovdje kada budu spremni za objavu.')"
                />
            @elseif ($galleryLayout === 'carousel')
                <div class="mt-6">
                        @if ($galleryEntries->count() > 1 || $galleryCarouselHasMore)
                            @include('niva-template::templates.classic.partials.carousel-controls', [
                                'name' => $galleryCarouselName,
                                'loadTarget' => $type,
                                'hasMore' => $galleryCarouselHasMore,
                            ])
                        @endif

                        <flux:carousel name="{{ $galleryCarouselName }}" class="-mx-4" :arrows="false" fade advance="page" track:class="px-4 scroll-px-4">
                            @foreach ($galleryEntries as $entry)
                                <flux:carousel.slide class="w-4/5 sm:w-1/2 lg:w-1/3" wire:key="gallery-carousel-{{ data_get($entry, 'id', $loop->index) }}">
                                    <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                                        'group flex h-full cursor-pointer flex-col overflow-hidden cx-public-surface cx-public-card-hover focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 dark:focus:ring-offset-zinc-950',
                                        'pointer-events-none' => ! $entry['url'],
                                    ])>
                                        <div class="overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                            @if ($entry['image'])
                                                <img src="{{ $entry['image'] }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="photo" />
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col p-5">
                                            <div class="flex items-start justify-between gap-4">
                                                <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                                @if ($entry['count_label'])
                                                    <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                                @endif
                                            </div>

                                            @if ($entry['description'])
                                                <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $entry['description'] }}</p>
                                            @endif
                                        </div>
                                    </a>
                                </flux:carousel.slide>
                            @endforeach
                        </flux:carousel>
                    </div>
            @elseif ($galleryLayout === 'text_cards')
                <div class="cx-public-section-content cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($galleryEntries as $entry)
                        @php
                            $entryTitle = (string) data_get($entry, 'title');
                            $entryDescriptionSource = data_get($entry, 'description');
                            $entryDescription = filled($entryDescriptionSource) ? str((string) $entryDescriptionSource)->stripTags()->squish()->limit(140)->toString() : null;
                            $entryImage = data_get($entry, 'image_large') ?: data_get($entry, 'image');
                            $entryUrl = data_get($entry, 'url');
                            $entryCountLabel = data_get($entry, 'count_label');
                        @endphp

                        <a href="{{ $entryUrl ?: '#' }}" title="{{ $entryTitle }}" aria-label="{{ $entryTitle }}" wire:key="gallery-text-card-{{ data_get($entry, 'id', $loop->index) }}" @class([
                            'group flex h-full cursor-pointer flex-col overflow-hidden cx-public-surface cx-public-focus',
                            'cx-public-card-hover' => $entryUrl,
                            'pointer-events-none' => ! $entryUrl,
                        ])>
                            <div class="flex min-h-[10.5rem] flex-1 flex-col px-6 pb-5 pt-6 sm:px-7 sm:pt-7">
                                <div class="flex items-start justify-between gap-5">
                                    <h3 class="cx-public-item-title text-zinc-950 cx-public-motion-color group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entryTitle }}</h3>

                                    <span class="cx-public-icon-frame cx-public-icon-frame-sm" aria-hidden="true">
                                        <flux:icon name="arrow-up-right" class="cx-public-icon-sm cx-public-motion-icon group-hover:-translate-y-0.5 group-hover:translate-x-0.5" />
                                    </span>
                                </div>

                                @if ($entryDescription)
                                    <p class="mt-3 line-clamp-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $entryDescription }}</p>
                                @endif

                                @if ($entryCountLabel)
                                    <div class="mt-auto pt-5">
                                        <span class="cx-public-badge-sm">
                                            <flux:icon name="photo" class="size-3.5" aria-hidden="true" />
                                            {{ $entryCountLabel }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <figure class="mx-3 mb-3 aspect-[16/10] cx-public-media-frame cx-public-border">
                                @if ($entryImage)
                                    <img src="{{ $entryImage }}" alt="{{ $entryTitle }}" class="cx-public-media-image cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="size-full" icon="photo" />
                                @endif
                            </figure>
                        </a>
                    @endforeach
                </div>
            @elseif ($galleryLayout === 'masonry')
                @php
                    $masonryRatios = ['aspect-[4/5]', 'aspect-square', 'aspect-[3/4]', 'aspect-[5/4]', 'aspect-[2/3]', 'aspect-[4/3]'];
                @endphp

                <div class="cx-public-section-content columns-2 gap-2 sm:columns-3 lg:columns-4">
                    @foreach ($galleryEntries as $entry)
                        @php
                            $entryTitle = (string) data_get($entry, 'title');
                            $entryImage = data_get($entry, 'image');
                            $entryUrl = data_get($entry, 'url');
                            $entryCountLabel = data_get($entry, 'count_label');
                            $entryRatio = $masonryRatios[$loop->index % count($masonryRatios)];
                        @endphp

                        <a href="{{ $entryUrl ?: '#' }}" title="{{ $entryTitle }}" aria-label="{{ $entryTitle }}" wire:key="gallery-masonry-{{ data_get($entry, 'id', $loop->index) }}" @class([
                            'group relative mb-2 block break-inside-avoid overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/5 transition duration-200 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:bg-zinc-900 dark:shadow-black/20 dark:focus:ring-offset-zinc-950',
                            'cursor-pointer hover:-translate-y-0.5 hover:shadow-md' => $entryUrl,
                            'pointer-events-none' => ! $entryUrl,
                        ])>
                            @if ($entryImage)
                                <img src="{{ $entryImage }}" alt="" class="{{ $entryRatio }} w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="{{ $entryRatio }} w-full" icon="photo" />
                            @endif

                            <div class="absolute inset-x-0 bottom-0 bg-zinc-950/72 p-3 text-white">
                                <h3 class="cx-public-small-strong transition group-hover:text-[color:var(--niva-primary-100)]">{{ $entryTitle }}</h3>
                                @if ($entryCountLabel)
                                    <p class="mt-1 text-xs font-medium leading-4 text-white/80">{{ $entryCountLabel }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @elseif ($galleryLayout === 'featured')
                @php
                    $featuredGallery = $galleryEntries->first();
                    $sideGalleries = $galleryEntries->slice(1, 2)->values();
                    $remainingGalleries = $galleryEntries->slice(3)->values();
                @endphp

                @if ($featuredGallery)
                    @php($featuredGalleryImage = data_get($featuredGallery, 'image_large') ?: data_get($featuredGallery, 'image'))
                    <div class="cx-public-section-content-spacious">
                        <div class="grid gap-7 lg:grid-cols-[minmax(0,1.28fr)_minmax(0,0.92fr)] lg:items-start">
                            <a href="{{ $featuredGallery['url'] ?: '#' }}" title="{{ $featuredGallery['title'] }}" aria-label="{{ $featuredGallery['title'] }}" @class([
                                'group block cursor-pointer rounded-xl transition duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950',
                                'pointer-events-none' => ! $featuredGallery['url'],
                            ])>
                                <figure class="overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/5 transition duration-200 dark:bg-zinc-900 dark:shadow-black/20">
                                    @if ($featuredGalleryImage)
                                        <img src="{{ $featuredGalleryImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="photo" />
                                    @endif
                                </figure>
                                <div class="mt-4 flex items-start justify-between gap-4 px-4">
                                    <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $featuredGallery['title'] }}</h3>
                                    @if ($featuredGallery['count_label'])
                                        <span class="shrink-0 cx-public-badge-sm">{{ $featuredGallery['count_label'] }}</span>
                                    @endif
                                </div>
                            </a>

                            @if ($sideGalleries->isNotEmpty())
                                <div class="grid gap-7">
                                    @foreach ($sideGalleries as $entry)
                                        <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                                            'group block cursor-pointer rounded-xl transition duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950',
                                            'pointer-events-none' => ! $entry['url'],
                                        ])>
                                            <figure class="overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/5 transition duration-200 dark:bg-zinc-900 dark:shadow-black/20">
                                                @if ($entry['image'])
                                                    <img src="{{ $entry['image'] }}" alt="" class="aspect-[16/8] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                                @else
                                                    <x-corexis::public-image-placeholder class="aspect-[16/8] w-full" icon="photo" />
                                                @endif
                                            </figure>
                                            <div class="mt-4 flex items-start justify-between gap-4 px-4">
                                                <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                                @if ($entry['count_label'])
                                                    <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($remainingGalleries->isNotEmpty())
                            <div class="cx-public-section-content-compact grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($remainingGalleries as $entry)
                                    <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                                        'group block cursor-pointer rounded-xl transition duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-950',
                                        'pointer-events-none' => ! $entry['url'],
                                    ])>
                                        <figure class="overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/5 transition duration-200 dark:bg-zinc-900 dark:shadow-black/20">
                                            @if ($entry['image'])
                                                <img src="{{ $entry['image'] }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="photo" />
                                            @endif
                                        </figure>
                                        <div class="mt-4 flex items-start justify-between gap-4 px-4">
                                            <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                            @if ($entry['count_label'])
                                                <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @elseif ($galleryLayout === 'wall')
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($galleryEntries as $entry)
                            <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                                'group relative min-h-72 cursor-pointer overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 cx-public-card-hover focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:bg-zinc-900 dark:shadow-black/20 dark:focus:ring-offset-zinc-950',
                                'pointer-events-none' => ! $entry['url'],
                            ])>
                                @if ($entry['image'])
                                    <img src="{{ $entry['image'] }}" alt="" class="absolute inset-0 size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="absolute inset-0 size-full" icon="photo" icon-class="size-10" />
                                @endif
                                <div class="absolute inset-x-0 bottom-0 p-4">
                                    <div class="rounded-xl bg-white/95 p-4 shadow-sm shadow-zinc-950/10 backdrop-blur-sm dark:bg-zinc-950/95 dark:shadow-black/30">
                                        <div class="flex items-start justify-between gap-3">
                                            <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                            @if ($entry['count_label'])
                                                <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                            @endif
                                        </div>
                                        @if ($entry['description'])
                                            <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $entry['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @elseif ($galleryLayout === 'journal')
                <div class="cx-public-section-content cx-public-stack">
                    @foreach ($galleryEntries as $entry)
                        <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                            'group grid cursor-pointer gap-4 cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:bg-zinc-950 dark:shadow-black/20 dark:focus:ring-offset-zinc-950 sm:grid-cols-[12rem_1fr] sm:items-center',
                            'pointer-events-none' => ! $entry['url'],
                        ])>
                            <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                @if ($entry['image'])
                                    <img src="{{ $entry['image'] }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="photo" icon-class="size-8" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <h3 class="cx-public-item-title tracking-tight text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                    @if ($entry['count_label'])
                                        <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                    @endif
                                </div>
                                @if ($entry['description'])
                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $entry['description'] }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="cx-public-section-content cx-public-grid sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($galleryEntries as $entry)
                        <a href="{{ $entry['url'] ?: '#' }}" title="{{ $entry['title'] }}" aria-label="{{ $entry['title'] }}" @class([
                            'group flex h-full cursor-pointer flex-col overflow-hidden cx-public-surface cx-public-card-hover focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary)] focus:ring-offset-2 focus:ring-offset-white dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 dark:focus:ring-offset-zinc-950',
                            'pointer-events-none' => ! $entry['url'],
                        ])>
                            @if ($entry['image'])
                                <img src="{{ $entry['image'] }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="photo" />
                            @endif
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $entry['title'] }}</h3>
                                    @if ($entry['count_label'])
                                        <span class="shrink-0 cx-public-badge-sm">{{ $entry['count_label'] }}</span>
                                    @endif
                                </div>
                                @if ($entry['description'])
                                    <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $entry['description'] }}</p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            @if ($galleryLayout !== 'carousel' && $this->hasMoreRecords())
                <div class="cx-public-section-content flex justify-center">
                    <button
                        type="button"
                        wire:click="loadMore"
                        wire:loading.attr="disabled"
                        wire:target="loadMore"
                        class="group cx-public-button-primary"
                    >
                        <span wire:loading.remove wire:target="loadMore">{{ $loadMoreLabel }}</span>
                        <span wire:loading wire:target="loadMore">{{ __('Učitavam...') }}</span>
                        <flux:icon name="arrow-down" class="size-4 transition duration-200 group-hover:translate-y-0.5" wire:loading.remove wire:target="loadMore" />
                        <flux:icon.loading class="size-4" wire:loading wire:target="loadMore" />
                    </button>
                </div>
            @endif
        <?php } ?>
