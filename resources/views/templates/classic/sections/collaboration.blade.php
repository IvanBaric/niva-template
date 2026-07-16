@php
    $collaborationItem = $items->first();
    $collaborationTitle = $collaborationItem?->localized('title');
    $collaborationDescription = $collaborationItem?->localized('content') ?: $collaborationItem?->localized('description');
    $collaborationButtonText = trim((string) data_get($section, 'settings.button_text', '')) ?: $collaborationItem?->localized('button_text');
    $collaborationButtonUrl = trim((string) data_get($section, 'settings.button_url', '')) ?: trim((string) data_get($collaborationItem, 'button_url', ''));
    $collaborationButtonUrl = $publicUrl->sanitize($collaborationButtonUrl) ?? '';
    $collaborationButtonPath = (string) parse_url($collaborationButtonUrl, PHP_URL_PATH);

    if ($collaborationButtonUrl !== '' && str_starts_with($collaborationButtonPath, '/app')) {
        $collaborationButtonUrl = $this->pageUrlForKey('contact') ?: '';
    }

    $showCollaborationButton = (bool) data_get($section, 'settings.show_button', true);
    $collaborationLayout = (string) data_get($section, 'settings.layout_variant', 'banner');
    $collaborationImage = $collaborationItem
        ? (method_exists($collaborationItem, 'imageUrl') ? $collaborationItem->imageUrl('thumb') : $assetUrl(data_get($collaborationItem, 'image')))
        : null;

    if (in_array($collaborationLayout, ['image_background', 'image_card'], true) && $collaborationItem) {
        $collaborationMedia = method_exists($collaborationItem, 'galleryFeaturedMedia')
            ? $collaborationItem->galleryFeaturedMedia('image')
            : null;

        if ($collaborationMedia && method_exists($collaborationMedia, 'getAvailableUrl')) {
            $collaborationImage = $collaborationMedia->getAvailableUrl(['xlarge', 'large', 'thumb']) ?: $collaborationImage;
        } elseif (method_exists($collaborationItem, 'imageUrl')) {
            $collaborationImage = $collaborationItem->imageUrl('xlarge')
                ?: $collaborationItem->imageUrl('large')
                ?: $collaborationImage;
        }
    }

    $hasCollaborationHeader = filled($eyebrow) || filled($title) || ($showSectionDescription && filled($description));
@endphp

@if ($collaborationItem)
    <div @class(['cx-public-section-content-compact' => $hasCollaborationHeader])>
        @if ($collaborationLayout === 'image_card')
            <div class="relative overflow-hidden rounded-xl bg-[color:var(--niva-primary-100)] shadow-sm ring-1 ring-zinc-950/5 dark:ring-white/10">
                @if ($collaborationImage)
                    <img src="{{ $collaborationImage }}" alt="" class="h-72 w-full object-cover sm:h-80 lg:absolute lg:inset-0 lg:h-full" loading="lazy" decoding="async">
                @else
                    <x-corexis::public-image-placeholder class="h-72 w-full sm:h-80 lg:absolute lg:inset-0 lg:h-full" icon="photo" icon-class="size-12" />
                @endif

                <div class="absolute inset-0 bg-[color:var(--niva-primary-950)] opacity-10"></div>
                <div class="absolute inset-y-0 right-0 hidden w-2/3 bg-gradient-to-l from-[color:var(--niva-primary-950)]/35 via-[color:var(--niva-primary-900)]/12 to-transparent lg:block"></div>

                <div class="relative -mt-12 flex px-4 pb-6 sm:px-6 lg:mt-0 lg:min-h-[27rem] lg:items-center lg:justify-end lg:px-10 lg:py-10">
                    <div class="w-full rounded-xl bg-white/95 p-5 text-zinc-950 shadow-sm shadow-zinc-950/10 ring-1 ring-white/80 backdrop-blur-sm sm:p-6 lg:w-[40%] lg:max-w-[28rem]">
                        @if ($collaborationTitle)
                            <h3 class="cx-public-featured-title text-zinc-950">{{ $collaborationTitle }}</h3>
                        @endif

                        @if ($collaborationDescription)
                            <p class="mt-3 cx-public-body text-zinc-600">{{ $collaborationDescription }}</p>
                        @endif

                        @if ($showCollaborationButton && $collaborationButtonText && $collaborationButtonUrl)
                            <a href="{{ $collaborationButtonUrl }}" class="mt-6 cx-public-button-primary">{{ $collaborationButtonText }}</a>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($collaborationLayout === 'image_background')
            <div class="relative min-h-[30rem] overflow-hidden rounded-xl bg-[color:var(--niva-primary-950)] shadow-sm ring-1 ring-zinc-950/5 dark:ring-white/10">
                @if ($collaborationImage)
                    <img src="{{ $collaborationImage }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy" decoding="async">
                @else
                    <x-corexis::public-image-placeholder class="absolute inset-0 size-full" icon="photo" icon-class="size-12" />
                @endif

                <div class="absolute inset-0 bg-[color:var(--niva-primary-950)] opacity-75 lg:hidden"></div>
                <div class="absolute inset-y-0 right-0 hidden w-[72%] bg-gradient-to-l from-[color:var(--niva-primary-950)] via-[color:var(--niva-primary-950)] to-transparent opacity-90 lg:block"></div>

                <div class="relative flex min-h-[30rem] items-center px-6 py-10 sm:px-8 lg:ml-auto lg:w-[54%] lg:px-12 lg:py-16">
                    <div class="cx-public-copy-narrow">
                        @if ($collaborationTitle)
                            <h3 class="cx-public-section-title text-white">{{ $collaborationTitle }}</h3>
                        @endif

                        @if ($collaborationDescription)
                            <p class="mt-4 cx-public-body text-white/85">{{ $collaborationDescription }}</p>
                        @endif

                        @if ($showCollaborationButton && $collaborationButtonText && $collaborationButtonUrl)
                            <a href="{{ $collaborationButtonUrl }}" class="mt-7 cx-public-button-inverse">{{ $collaborationButtonText }}</a>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="overflow-hidden rounded-xl bg-[color:var(--niva-primary-950)] shadow-sm ring-1 ring-zinc-950/5 dark:ring-white/10">
                <div class="grid lg:grid-cols-[0.52fr_0.48fr]">
                    <div class="min-h-64 bg-[color:var(--niva-primary-100)] lg:min-h-[25rem]">
                        @if ($collaborationImage)
                            <img src="{{ $collaborationImage }}" alt="" class="h-full min-h-64 w-full object-cover lg:min-h-[25rem]" loading="lazy" decoding="async">
                        @else
                            <x-corexis::public-image-placeholder class="h-full min-h-64 w-full lg:min-h-[25rem]" icon="photo" icon-class="size-12" />
                        @endif
                    </div>

                    <div class="flex items-center bg-[color:var(--niva-primary-900)] px-6 py-9 text-white sm:px-8 sm:py-10 lg:px-12 lg:py-14">
                        <div class="cx-public-copy-narrow">
                            @if ($collaborationTitle)
                                <h3 class="cx-public-section-title text-white">{{ $collaborationTitle }}</h3>
                            @endif

                            @if ($collaborationDescription)
                                <p class="mt-4 cx-public-body text-white/80">{{ $collaborationDescription }}</p>
                            @endif

                            @if ($showCollaborationButton && $collaborationButtonText && $collaborationButtonUrl)
                                <a href="{{ $collaborationButtonUrl }}" class="mt-7 cx-public-button-inverse">{{ $collaborationButtonText }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
