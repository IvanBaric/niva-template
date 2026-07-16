@php
    $videoLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
    $videoLayout = in_array($videoLayout, ['cards', 'featured', 'list', 'focus', 'grid_3x2', 'grid_4x2'], true) ? $videoLayout : 'cards';
    $videoModalName = 'section-video-'.$section->uuid;
    $videoCards = $items
        ->map(function ($item) use ($title, $publicUrl) {
            $videoUrl = (string) ($item->setting('youtube_url') ?: data_get($item, 'url') ?: $item->setting('embed_url'));
            $video = \IvanBaric\Pages\Support\YouTubeVideo::fromUrl($videoUrl);

            if ($video === null) {
                return null;
            }

            $embedUrl = $publicUrl->sanitize($video['embed_url']);
            $thumbnailUrl = $publicUrl->sanitize($video['thumbnail_url']);

            if ($embedUrl === null) {
                return null;
            }

            $videoTitle = $item->localized('title') ?: $title ?: __('Video');
            $videoDescription = trim((string) ($item->localized('description') ?: $item->localized('content')));

            return [
                'title' => $videoTitle,
                'description' => $videoDescription,
                'embed_url' => $embedUrl,
                'thumbnail_url' => $thumbnailUrl ?? '',
            ];
        })
        ->filter()
        ->values();
    $primaryVideo = $videoCards->first();
    $secondaryVideos = $videoCards->slice(1)->values();
@endphp

@if ($videoCards->isNotEmpty())
    <div x-data="{ iframeSrc: null, modalTitle: '' }">
        @if ($videoLayout === 'featured' && $primaryVideo)
            <div @class([
                'cx-public-section-content cx-public-grid',
                'lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]' => $secondaryVideos->isNotEmpty(),
                'cx-public-container-article' => $secondaryVideos->isEmpty(),
            ])>
                <article class="group overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                    <flux:modal.trigger :name="$videoModalName">
                        <button type="button" x-on:click="iframeSrc = @js($primaryVideo['embed_url']); modalTitle = @js($primaryVideo['title'])" title="{{ $primaryVideo['title'] }}" aria-label="{{ $primaryVideo['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white">
                            @if ($primaryVideo['thumbnail_url'] !== '')
                                <img src="{{ $primaryVideo['thumbnail_url'] }}" alt="{{ $primaryVideo['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-10" />
                            @endif
                            <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                            <span class="absolute inset-0 flex items-center justify-center">
                                <span class="inline-flex size-16 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                    <flux:icon name="play" class="ml-0.5 size-7" />
                                </span>
                            </span>
                        </button>
                    </flux:modal.trigger>

                    <div class="p-5 sm:p-6">
                        <h3 class="cx-public-item-title text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $primaryVideo['title'] }}</h3>
                        @if ($primaryVideo['description'] !== '')
                            <p class="mt-2 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $primaryVideo['description'] }}</p>
                        @endif
                    </div>
                </article>

                @if ($secondaryVideos->isNotEmpty())
                    <div class="cx-public-grid-tight">
                        @foreach ($secondaryVideos as $video)
                            <article class="group grid overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 sm:grid-cols-[8.5rem_minmax(0,1fr)] lg:grid-cols-1 xl:grid-cols-[8.5rem_minmax(0,1fr)]">
                                <div class="min-w-0">
                                    <flux:modal.trigger :name="$videoModalName">
                                        <button type="button" x-on:click="iframeSrc = @js($video['embed_url']); modalTitle = @js($video['title'])" title="{{ $video['title'] }}" aria-label="{{ $video['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white">
                                            @if ($video['thumbnail_url'] !== '')
                                                <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            @else
                                                <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-8" />
                                            @endif
                                            <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                                            <span class="absolute inset-0 flex items-center justify-center">
                                                <span class="inline-flex size-10 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                                    <flux:icon name="play" class="ml-0.5 size-4" />
                                                </span>
                                            </span>
                                        </button>
                                    </flux:modal.trigger>
                                </div>

                                <div class="min-w-0 p-4">
                                    <h3 class="cx-public-meta-strong text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $video['title'] }}</h3>
                                    @if ($video['description'] !== '')
                                        <p class="mt-1 line-clamp-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $video['description'] }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @elseif ($videoLayout === 'list')
            <div class="cx-public-section-content cx-public-grid-compact">
                @foreach ($videoCards as $video)
                    <article class="group grid overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 sm:grid-cols-[minmax(12rem,18rem)_minmax(0,1fr)]">
                        <div class="min-w-0">
                            <flux:modal.trigger :name="$videoModalName">
                                <button type="button" x-on:click="iframeSrc = @js($video['embed_url']); modalTitle = @js($video['title'])" title="{{ $video['title'] }}" aria-label="{{ $video['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white sm:h-full sm:min-h-40">
                                    @if ($video['thumbnail_url'] !== '')
                                        <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-8" />
                                    @endif
                                    <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                                    <span class="absolute inset-0 flex items-center justify-center">
                                        <span class="inline-flex size-14 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                            <flux:icon name="play" class="ml-0.5 size-6" />
                                        </span>
                                    </span>
                                </button>
                            </flux:modal.trigger>
                        </div>

                        <div class="min-w-0 p-5">
                            <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $video['title'] }}</h3>
                            @if ($video['description'] !== '')
                                <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $video['description'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif ($videoLayout === 'focus' && $primaryVideo)
            <div class="cx-public-section-content cx-public-stack-loose">
                <article class="group cx-public-container-article overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                    <flux:modal.trigger :name="$videoModalName">
                        <button type="button" x-on:click="iframeSrc = @js($primaryVideo['embed_url']); modalTitle = @js($primaryVideo['title'])" title="{{ $primaryVideo['title'] }}" aria-label="{{ $primaryVideo['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white">
                            @if ($primaryVideo['thumbnail_url'] !== '')
                                <img src="{{ $primaryVideo['thumbnail_url'] }}" alt="{{ $primaryVideo['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-10" />
                            @endif
                            <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                            <span class="absolute inset-0 flex items-center justify-center">
                                <span class="inline-flex size-16 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                    <flux:icon name="play" class="ml-0.5 size-7" />
                                </span>
                            </span>
                        </button>
                    </flux:modal.trigger>

                    <div class="p-5 text-center sm:p-6">
                        <h3 class="cx-public-item-title text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $primaryVideo['title'] }}</h3>
                        @if ($primaryVideo['description'] !== '')
                            <p class="mt-2 cx-public-copy-narrow-centered cx-public-body text-zinc-600 dark:text-zinc-300">{{ $primaryVideo['description'] }}</p>
                        @endif
                    </div>
                </article>

                @if ($secondaryVideos->isNotEmpty())
                    <div class="cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($secondaryVideos as $video)
                            <article class="group overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                <flux:modal.trigger :name="$videoModalName">
                                    <button type="button" x-on:click="iframeSrc = @js($video['embed_url']); modalTitle = @js($video['title'])" title="{{ $video['title'] }}" aria-label="{{ $video['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white">
                                        @if ($video['thumbnail_url'] !== '')
                                            <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                        @else
                                            <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-8" />
                                        @endif
                                        <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                                        <span class="absolute inset-0 flex items-center justify-center">
                                            <span class="inline-flex size-12 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                                <flux:icon name="play" class="ml-0.5 size-5" />
                                            </span>
                                        </span>
                                    </button>
                                </flux:modal.trigger>

                                <div class="p-4">
                                    <h3 class="cx-public-meta-strong text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $video['title'] }}</h3>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div @class([
                'cx-public-section-content',
                'cx-public-grid md:grid-cols-2' => $videoLayout === 'cards',
                'cx-public-grid sm:grid-cols-2 lg:grid-cols-3' => $videoLayout === 'grid_3x2',
                'cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4' => $videoLayout === 'grid_4x2',
            ])>
                @foreach ($videoCards as $video)
                    <article class="group overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                        <flux:modal.trigger :name="$videoModalName">
                            <button type="button" x-on:click="iframeSrc = @js($video['embed_url']); modalTitle = @js($video['title'])" title="{{ $video['title'] }}" aria-label="{{ $video['title'] }}" class="relative block aspect-video w-full cursor-pointer overflow-hidden bg-zinc-900 text-white">
                                @if ($video['thumbnail_url'] !== '')
                                    <img src="{{ $video['thumbnail_url'] }}" alt="{{ $video['title'] }}" class="size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="size-full" icon="film" icon-class="size-10" />
                                @endif
                                <span class="absolute inset-0 bg-zinc-950/20 transition group-hover:bg-zinc-950/10"></span>
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="inline-flex size-16 items-center justify-center rounded-full bg-white/95 text-zinc-950 shadow-sm shadow-zinc-950/20 ring-1 ring-white/80 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-800)]">
                                        <flux:icon name="play" class="ml-0.5 size-7" />
                                    </span>
                                </span>
                            </button>
                        </flux:modal.trigger>

                        <div class="p-5">
                            <h3 class="cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $video['title'] }}</h3>
                            @if ($video['description'] !== '')
                                <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $video['description'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif

        <flux:modal :name="$videoModalName" class="w-[calc(100vw-2rem)] max-w-5xl" x-on:close="iframeSrc = null; modalTitle = ''">
            <div class="cx-public-stack-compact">
                <h3 class="line-clamp-2 cx-public-item-title-compact text-zinc-950 dark:text-white" x-text="modalTitle"></h3>
                <div class="aspect-video max-h-[calc(100vh-10rem)] overflow-hidden rounded-xl bg-black">
                    <template x-if="iframeSrc">
                        <iframe x-bind:src="iframeSrc" x-bind:title="modalTitle" class="size-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                    </template>
                </div>
            </div>
        </flux:modal>
    </div>
@endif
