        <?php if ($type === 'features') { ?>
            @php
                $featuresLayout = (string) data_get($section, 'settings.layout_variant', 'mosaic');
                $featuresLayout = in_array($featuresLayout, ['mosaic', 'photo_cards', 'editorial', 'spotlight', 'alternating', 'path', 'studio'], true) ? $featuresLayout : 'mosaic';
                $featureImageUrl = function (mixed $item, string $conversion = 'large') use ($assetUrl): ?string {
                    if (is_object($item) && method_exists($item, 'imageUrl')) {
                        $url = $item->imageUrl($conversion);

                        if (filled($url)) {
                            return $url;
                        }
                    }

                    return $assetUrl(data_get($item, 'image'));
                };
            @endphp

            @if ($featuresLayout === 'editorial')
                <div class="cx-public-section-content-spacious cx-public-stack-showcase">
                    @foreach ($items as $item)
                        @php
                            $featureImage = $featureImageUrl($item, 'xlarge');
                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->toString() : null;
                        @endphp

                        <article>
                            <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                @if ($featureImage)
                                    <img src="{{ $featureImage }}" alt="" class="aspect-[16/5] w-full object-cover sm:aspect-[18/5]" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[16/5] w-full sm:aspect-[18/5]" :icon="$item->icon ?: 'sparkles'" icon-class="size-9" />
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
            @elseif ($featuresLayout === 'spotlight')
                @php
                    $spotlightItem = $items->first();
                    $spotlightRest = $items->slice(1)->values();
                @endphp

                @if ($spotlightItem)
                    @php
                        $spotlightImage = $featureImageUrl($spotlightItem, 'xlarge');
                        $spotlightTextSource = $spotlightItem->localized('description') ?: $spotlightItem->localized('content');
                        $spotlightText = filled($spotlightTextSource) ? str((string) $spotlightTextSource)->stripTags()->squish()->toString() : null;
                    @endphp

                    <div class="cx-public-section-content-spacious">
                        <article class="grid gap-8 lg:grid-cols-[minmax(0,1.08fr)_minmax(18rem,0.72fr)] lg:items-center lg:gap-12">
                            <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                @if ($spotlightImage)
                                    <img src="{{ $spotlightImage }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[16/9] w-full" :icon="$spotlightItem->icon ?: 'sparkles'" icon-class="size-10" />
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
                                            $featureImage = $featureImageUrl($item, 'large');
                                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->toString() : null;
                                        @endphp

                                        <article>
                                            <figure class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                                @if ($featureImage)
                                                    <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                @else
                                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: 'sparkles'" icon-class="size-7" />
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
            @elseif ($featuresLayout === 'alternating')
                <div class="cx-public-section-content-spacious cx-public-stack-editorial">
                    @foreach ($items as $item)
                        @php
                            $featureImage = $featureImageUrl($item, 'xlarge');
                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->toString() : null;
                        @endphp

                        <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,0.9fr)] lg:items-center lg:gap-10">

                            <figure @class([
                                'overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900',
                                'lg:order-2' => $loop->even,
                            ])>
                                @if ($featureImage)
                                    <img src="{{ $featureImage }}" alt="" class="aspect-[16/6] w-full object-cover lg:aspect-[18/5]" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[16/6] w-full lg:aspect-[18/5]" :icon="$item->icon ?: 'sparkles'" icon-class="size-8" />
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
            @elseif ($featuresLayout === 'path')
                <div class="cx-public-section-content-spacious cx-public-stack-editorial">
                    @foreach ($items as $item)
                        @php
                            $featureImage = $featureImageUrl($item, 'large');
                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->toString() : null;
                        @endphp
                        <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1fr)] lg:items-center">
                            <figure @class([
                                'overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/20',
                                'lg:order-2' => $loop->even,
                            ])>
                                @if ($featureImage)
                                    <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: 'sparkles'" icon-class="size-10" />
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
            @elseif ($featuresLayout === 'photo_cards')
                <div class="cx-public-section-content cx-public-grid-loose sm:grid-cols-2 xl:grid-cols-4">
                    @foreach ($items as $item)
                        @php
                            $featureImage = $featureImageUrl($item, 'large');
                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->limit(150)->toString() : null;
                        @endphp

                        <article class="group flex h-full flex-col overflow-hidden cx-public-surface-plain cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                            <figure class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                @if ($featureImage)
                                    <img src="{{ $featureImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: 'sparkles'" icon-class="size-9" />
                                @endif
                            </figure>

                            <div class="relative flex flex-1 flex-col px-5 pb-6 pt-8">
                                <span class="absolute -top-7 left-5 inline-flex size-14 items-center justify-center rounded-xl bg-[color:var(--niva-primary-700)] text-white shadow-md shadow-zinc-950/15 ring-4 ring-white dark:ring-zinc-950">
                                    <flux:icon :name="$item->icon ?: 'sparkles'" class="size-6" />
                                </span>

                                <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                @if ($featureText)
                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $featureText }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif ($featuresLayout === 'studio')
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $item)
                            @php
                                $featureImage = $featureImageUrl($item, 'large');
                                $featureTextSource = $item->localized('description') ?: $item->localized('content');
                                $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->limit(150)->toString() : null;
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
                                        <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: 'sparkles'" icon-class="size-9" />
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
            @else
                <div class="cx-public-section-content cx-public-grid-loose md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $featureImage = $featureImageUrl($item, 'large');
                            $featureTextSource = $item->localized('description') ?: $item->localized('content');
                            $featureText = filled($featureTextSource) ? str((string) $featureTextSource)->stripTags()->squish()->toString() : null;
                        @endphp
                        <article class="group flex h-full flex-col overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                            <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                @if ($featureImage)
                                    <img src="{{ $featureImage }}" alt="" class="aspect-[16/9] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[16/9] w-full" :icon="$item->icon ?: 'sparkles'" icon-class="size-10" />
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
        <?php } ?>
