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
    $collaborationImage = null;

    if ($collaborationItem) {
        $collaborationMedia = method_exists($collaborationItem, 'galleryFeaturedMedia')
            ? $collaborationItem->galleryFeaturedMedia('image')
            : null;

        if ($collaborationMedia && method_exists($collaborationMedia, 'getAvailableUrl')) {
            $collaborationImage = $collaborationMedia->getAvailableUrl(['xlarge', 'large', 'thumb']);
        } elseif (method_exists($collaborationItem, 'imageUrl')) {
            $collaborationImage = $collaborationItem->imageUrl('xlarge')
                ?: $collaborationItem->imageUrl('large')
                ?: $collaborationItem->imageUrl('thumb');
        } else {
            $collaborationImage = $assetUrl(data_get($collaborationItem, 'image'));
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
        @elseif ($collaborationLayout === 'editorial_frame')
            <div class="relative isolate overflow-hidden rounded-[1.75rem] border border-[color:var(--niva-primary-100)] bg-[linear-gradient(135deg,var(--niva-primary-50),#ffffff_55%,var(--niva-primary-100))] shadow-xl shadow-zinc-950/10 dark:border-[color:var(--niva-primary-900)] dark:bg-[linear-gradient(135deg,var(--niva-primary-950),#18181b_58%,var(--niva-primary-950))] dark:shadow-black/25">
                <div class="absolute -left-20 -top-24 -z-10 size-64 rounded-full bg-[color:var(--niva-primary-200)]/45 blur-3xl dark:bg-[color:var(--niva-primary-800)]/20" aria-hidden="true"></div>
                <div class="absolute -bottom-20 left-1/3 -z-10 size-52 rounded-full bg-white/80 blur-3xl dark:bg-white/5" aria-hidden="true"></div>

                <div class="grid items-center lg:grid-cols-[0.9fr_1.1fr]">
                    <div class="flex items-center px-6 py-10 sm:px-9 sm:py-12 lg:px-14 lg:py-16">
                        <div class="cx-public-copy-narrow">
                            <span class="mb-6 flex items-center gap-2" aria-hidden="true">
                                <span class="h-px w-10 bg-[color:var(--niva-primary)]"></span>
                                <span class="size-1.5 rounded-full bg-[color:var(--niva-primary)]"></span>
                            </span>

                            @if ($collaborationTitle)
                                <h3 class="cx-public-section-title text-zinc-950 dark:text-white">{{ $collaborationTitle }}</h3>
                            @endif

                            @if ($collaborationDescription)
                                <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $collaborationDescription }}</p>
                            @endif

                            @if ($showCollaborationButton && $collaborationButtonText && $collaborationButtonUrl)
                                <a href="{{ $collaborationButtonUrl }}" class="mt-7 cx-public-button-primary">{{ $collaborationButtonText }}</a>
                            @endif
                        </div>
                    </div>

                    <div class="relative p-4 pt-0 sm:p-6 sm:pt-0 lg:p-7 lg:pl-0">
                        <div class="absolute bottom-1 left-1 right-7 top-7 rounded-[1.5rem] border border-[color:var(--niva-primary-300)]/70 dark:border-[color:var(--niva-primary-700)]/50" aria-hidden="true"></div>
                        <figure class="relative aspect-[4/3] overflow-hidden rounded-[1.5rem] bg-[color:var(--niva-primary-100)] shadow-2xl shadow-zinc-950/15 ring-4 ring-white/90 dark:bg-[color:var(--niva-primary-950)] dark:shadow-black/35 dark:ring-white/10">
                            @if ($collaborationImage)
                                <img src="{{ $collaborationImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="size-full" icon="photo" icon-class="size-12" />
                            @endif
                        </figure>
                    </div>
                </div>
            </div>
        @elseif ($collaborationLayout === 'panorama')
            <div class="overflow-hidden rounded-[1.75rem] border border-zinc-200/80 bg-white shadow-xl shadow-zinc-950/10 dark:border-white/10 dark:bg-zinc-950 dark:shadow-black/30">
                <div class="relative min-h-64 overflow-hidden bg-[color:var(--niva-primary-100)] sm:aspect-[16/7]">
                    @if ($collaborationImage)
                        <img src="{{ $collaborationImage }}" alt="" class="absolute inset-0 size-full object-cover" loading="lazy" decoding="async">
                    @else
                        <x-corexis::public-image-placeholder class="absolute inset-0 size-full" icon="photo" icon-class="size-12" />
                    @endif
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-[color:var(--niva-primary-950)]/35 to-transparent" aria-hidden="true"></div>
                </div>

                <div class="relative isolate overflow-hidden bg-[color:var(--niva-primary-950)] px-6 py-8 text-white sm:px-9 sm:py-9 lg:px-12">
                    <div class="absolute -right-16 -top-24 -z-10 size-56 rounded-full bg-[color:var(--niva-primary)]/30 blur-3xl" aria-hidden="true"></div>
                    <div class="absolute inset-x-10 top-0 h-px bg-gradient-to-r from-transparent via-white/45 to-transparent" aria-hidden="true"></div>

                    <div class="grid items-center gap-6 lg:grid-cols-[minmax(0,1fr)_auto] lg:gap-10">
                        <div class="max-w-3xl">
                            @if ($collaborationTitle)
                                <h3 class="cx-public-section-title text-white">{{ $collaborationTitle }}</h3>
                            @endif

                            @if ($collaborationDescription)
                                <p class="mt-3 cx-public-body text-white/75">{{ $collaborationDescription }}</p>
                            @endif
                        </div>

                        @if ($showCollaborationButton && $collaborationButtonText && $collaborationButtonUrl)
                            <a href="{{ $collaborationButtonUrl }}" class="cx-public-button-inverse shrink-0 lg:justify-self-end">{{ $collaborationButtonText }}</a>
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
