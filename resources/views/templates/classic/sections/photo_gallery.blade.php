@php
    $photoLayout = (string) data_get($section, 'settings.layout_variant', 'grid');
    $photoLayout = match ($photoLayout) {
        'photo_featured' => 'featured',
        'photo_carousel' => 'carousel',
        default => $photoLayout,
    };
    $photoLayout = in_array($photoLayout, ['grid_2x2', 'grid_3x3', 'grid', 'featured', 'mosaic', 'carousel'], true) ? $photoLayout : 'grid';
    $photoCarouselName = 'photo-gallery-carousel-'.(string) data_get($section, 'uuid', data_get($section, 'id', 'section'));
    $showCaptions = (bool) data_get($section, 'settings.show_captions', false);
    $imageRatio = (string) data_get($section, 'settings.image_ratio', 'four_three');
    $aspectClass = match ($imageRatio) {
        'square' => 'aspect-square',
        'three_two' => 'aspect-[3/2]',
        'video' => 'aspect-video',
        default => 'aspect-[4/3]',
    };

    $mediaItems = method_exists($section, 'galleryMedia')
        ? $section->galleryMedia('images')->values()
        : collect();

    $mediaUrl = static function (mixed $media, array $preferred): ?string {
        if (! is_object($media) || ! method_exists($media, 'getUrl')) {
            return null;
        }

        foreach (array_filter($preferred) as $conversion) {
            try {
                if (method_exists($media, 'getAvailableUrl')) {
                    $url = $media->getAvailableUrl([(string) $conversion]);

                    if (filled($url)) {
                        return $url;
                    }
                }

                if (method_exists($media, 'hasGeneratedConversion') && ! $media->hasGeneratedConversion((string) $conversion)) {
                    continue;
                }

                $url = $media->getUrl((string) $conversion);

                if (filled($url)) {
                    return $url;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return $media->getUrl();
        } catch (\Throwable) {
            return null;
        }
    };

    $photos = $mediaItems
        ->map(function (mixed $media, int $index) use ($mediaUrl, $title): ?array {
            $main = $mediaUrl($media, ['large']);
            $lightbox = $mediaUrl($media, ['xlarge', 'large']);
            $thumb = $mediaUrl($media, ['thumb']);

            if (! $main) {
                return null;
            }

            $fallbackAlt = ($title ?: __('Foto galerija')).' '.__('fotografija').' '.($index + 1);
            $caption = trim((string) ($media->getCustomProperty('caption', '') ?: $media->getCustomProperty('title', '') ?: ''));
            $alt = method_exists($media, 'altText') ? $media->altText($fallbackAlt) : (string) ($media->name ?? $fallbackAlt);

            return [
                'main' => $main,
                'lightbox' => $lightbox ?: $main,
                'thumb' => $thumb ?: $main,
                'alt' => $alt,
                'caption' => $caption,
            ];
        })
        ->filter()
        ->values();

    $lightboxItems = $photos
        ->map(fn (array $photo): array => [
            'url' => $photo['lightbox'],
            'alt' => $photo['alt'],
        ])
        ->all();
@endphp

<div
    wire:key="photo-gallery-{{ data_get($section, 'uuid', data_get($section, 'id', 'section')) }}-{{ $photoLayout }}"
    @class([
        'mt-6' => $photoLayout === 'carousel',
        'cx-public-section-content' => $photoLayout !== 'carousel',
    ])
>
    @if ($photos->isEmpty())
        <x-public-section-empty-state
            :section="$section"
            icon="photo"
            :title="__('Fotografije uskoro')"
            :description="__('Fotografije će se prikazati ovdje kada budu spremne za objavu.')"
        />
    @else
        <div
            x-data="{
                open: false,
                index: 0,
                imageVisible: true,
                items: @js($lightboxItems),
                show(position) {
                    this.index = position;
                    this.imageVisible = true;
                    this.open = true;
                    this.$nextTick(() => this.$refs.closeButton?.focus());
                },
                close() {
                    this.open = false;
                },
                go(position) {
                    if (position === this.index || this.items.length === 0) {
                        return;
                    }

                    this.imageVisible = false;

                    window.setTimeout(() => {
                        this.index = position;
                        this.$nextTick(() => {
                            this.imageVisible = true;
                        });
                    }, 120);
                },
                next() {
                    this.go((this.index + 1) % this.items.length);
                },
                previous() {
                    this.go((this.index - 1 + this.items.length) % this.items.length);
                },
            }"
            x-on:keydown.escape.window="open && close()"
            x-on:keydown.arrow-right.window="open && next()"
            x-on:keydown.arrow-left.window="open && previous()"
            x-effect="
                document.documentElement.classList.toggle('overflow-hidden', open);
                document.body.classList.toggle('overflow-hidden', open);
            "
        >
            @if ($photoLayout === 'featured')
                @php
                    $featuredPhoto = $photos->first();
                    $sidePhotos = $photos->slice(1)->values();
                @endphp

                <div class="cx-public-grid-compact lg:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">
                    <x-gallery::public-photo-trigger
                        :src="$featuredPhoto['main']"
                        :alt="$featuredPhoto['alt']"
                        image-class="aspect-[4/3] w-full object-cover"
                        :caption="$showCaptions ? $featuredPhoto['caption'] : null"
                        x-on:click="show(0)"
                    />

                    <div class="cx-public-grid-compact sm:grid-cols-2">
                        @foreach ($sidePhotos as $photo)
                            <x-gallery::public-photo-trigger
                                :src="$photo['main']"
                                :alt="$photo['alt']"
                                image-class="{{ $aspectClass }} w-full object-cover"
                                :caption="$showCaptions ? $photo['caption'] : null"
                                x-on:click="show({{ $loop->index + 1 }})"
                            />
                        @endforeach
                    </div>
                </div>
            @elseif ($photoLayout === 'mosaic')
                <div class="grid auto-rows-[9rem] grid-cols-2 gap-4 sm:auto-rows-[11rem] lg:grid-cols-4">
                    @foreach ($photos as $photo)
                        <x-gallery::public-photo-trigger
                            :src="$photo['main']"
                            :alt="$photo['alt']"
                            image-class="size-full object-cover"
                            frame-class="size-full"
                            caption-mode="overlay"
                            :caption="$showCaptions ? $photo['caption'] : null"
                            x-on:click="show({{ $loop->index }})"
                            @class([
                                'sm:col-span-2 sm:row-span-2' => $loop->first,
                                'lg:col-span-2' => $loop->iteration === 4,
                            ])
                        />
                    @endforeach
                </div>
            @elseif ($photoLayout === 'carousel')
                @if ($photos->count() > 1)
                    <div class="mb-4 flex justify-end">
                        <flux:carousel.controls name="{{ $photoCarouselName }}" />
                    </div>
                @endif

                <flux:carousel name="{{ $photoCarouselName }}" class="-mx-4" :arrows="false" fade advance="page" track:class="px-4 scroll-px-4">
                    @foreach ($photos as $photo)
                        <flux:carousel.slide class="w-4/5 sm:w-1/2 lg:w-1/3" wire:key="photo-gallery-carousel-{{ data_get($section, 'uuid', data_get($section, 'id', 'section')) }}-{{ $loop->index }}">
                            <x-gallery::public-photo-trigger
                                :src="$photo['main']"
                                :alt="$photo['alt']"
                                image-class="{{ $aspectClass }} w-full object-cover"
                                :caption="$showCaptions ? $photo['caption'] : null"
                                class="w-full"
                                x-on:click="show({{ $loop->index }})"
                            />
                        </flux:carousel.slide>
                    @endforeach
                </flux:carousel>
            @elseif ($photoLayout === 'grid_2x2' || $photoLayout === 'grid_3x3')
                <div @class([
                    'cx-public-grid-compact',
                    'sm:grid-cols-2' => $photoLayout === 'grid_2x2',
                    'sm:grid-cols-2 lg:grid-cols-3' => $photoLayout === 'grid_3x3',
                ])>
                    @foreach ($photos as $photo)
                        <x-gallery::public-photo-trigger
                            :src="$photo['main']"
                            :alt="$photo['alt']"
                            image-class="{{ $aspectClass }} w-full object-cover"
                            :caption="$showCaptions ? $photo['caption'] : null"
                            x-on:click="show({{ $loop->index }})"
                        />
                    @endforeach
                </div>
            @else
                <div class="cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($photos as $photo)
                        <x-gallery::public-photo-trigger
                            :src="$photo['main']"
                            :alt="$photo['alt']"
                            image-class="{{ $aspectClass }} w-full object-cover"
                            :caption="$showCaptions ? $photo['caption'] : null"
                            x-on:click="show({{ $loop->index }})"
                        />
                    @endforeach
                </div>
            @endif

            <x-gallery::public-lightbox :media-items="$mediaItems" :title="$title ?: __('Foto galerija')" />
        </div>
    @endif
</div>
