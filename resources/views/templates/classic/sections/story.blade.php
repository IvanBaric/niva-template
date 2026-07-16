        <?php if ($isStorySection) { ?>
            @php
                $storyDefaultLayout = $type === 'values' ? 'cards' : 'media_right';
                $storyLayouts = $type === 'values'
                    ? ['path', 'cards', 'showcase', 'journal']
                    : ['media_right', 'media_left', 'path', 'cards', 'showcase', 'journal'];
                $storyLayout = (string) data_get($section, 'settings.layout_variant', $storyDefaultLayout);
                $storyLayout = $storyLayout !== '' ? $storyLayout : $storyDefaultLayout;

                if ($type !== 'values') {
                    $storyLayout = in_array($storyLayout, $storyLayouts, true) ? $storyLayout : $storyDefaultLayout;
                }

                $fallbackIcon = match ($type) {
                    'mission' => 'sparkles',
                    'vision' => 'sun',
                    'team' => 'users',
                    default => 'heart',
                };
                $storyImageFor = fn ($item) => method_exists($item, 'imageUrl')
                    ? $item->imageUrl('large')
                    : $assetUrl(data_get($item, 'image'));
            @endphp

            @if ($storyLayout === 'cards')
                <div class="cx-public-section-content cx-public-grid-compact md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $itemImage = $storyImageFor($item);
                            $itemTextSource = $item->localized('description') ?: $item->localized('content');
                            $itemText = filled($itemTextSource) ? str((string) $itemTextSource)->stripTags()->squish()->toString() : null;
                        @endphp
                        <article class="flex h-full flex-col overflow-hidden cx-public-surface">
                            @if ($itemImage)
                                <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$item->icon ?: $fallbackIcon" icon-class="size-7" />
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
            @elseif ($storyLayout === 'showcase')
                @php
                    $featuredStoryItem = $items->first();
                    $secondaryStoryItems = $items->slice(1);
                @endphp

                <div class="cx-public-section-content cx-public-grid lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)]">
                    @if ($featuredStoryItem)
                        @php
                            $itemImage = $storyImageFor($featuredStoryItem);
                            $itemTextSource = $featuredStoryItem->localized('description') ?: $featuredStoryItem->localized('content');
                            $itemText = filled($itemTextSource) ? str((string) $itemTextSource)->stripTags()->squish()->toString() : null;
                        @endphp
                        <article class="overflow-hidden cx-public-surface">
                            @if ($itemImage)
                                <img src="{{ $itemImage }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="aspect-[16/10]" :icon="$featuredStoryItem->icon ?: $fallbackIcon" icon-class="size-10" />
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
                                $itemImage = $storyImageFor($item);
                                $itemTextSource = $item->localized('description') ?: $item->localized('content');
                                $itemText = filled($itemTextSource) ? str((string) $itemTextSource)->stripTags()->squish()->toString() : null;
                            @endphp
                            <article class="cx-public-grid-compact cx-public-surface cx-public-card-padding-compact sm:grid-cols-[8rem_1fr] sm:items-center">
                                @if ($itemImage)
                                    <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full rounded-xl object-cover sm:h-28" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full rounded-xl sm:h-28" :icon="$item->icon ?: $fallbackIcon" icon-class="size-6" />
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
            @elseif ($storyLayout === 'journal')
                <div class="cx-public-section-content cx-public-stack">
                    @foreach ($items as $item)
                        @php
                            $itemImage = $storyImageFor($item);
                            $itemTextSource = $item->localized('description') ?: $item->localized('content');
                            $itemText = filled($itemTextSource) ? str((string) $itemTextSource)->stripTags()->squish()->toString() : null;
                        @endphp
                        <article class="cx-public-grid cx-public-surface cx-public-card-padding-compact sm:grid-cols-[11rem_1fr] sm:items-center">
                            @if ($itemImage)
                                <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full rounded-xl object-cover sm:h-36" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full rounded-xl sm:h-36" :icon="$item->icon ?: $fallbackIcon" icon-class="size-7" />
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
            @elseif ($storyLayout === 'path' || ($type !== 'values' && in_array($storyLayout, ['media_right', 'media_left'], true)))
                <div class="cx-public-section-content-spacious cx-public-stack-editorial">
                    @foreach ($items as $item)
                        @php
                            $itemImage = $storyImageFor($item);
                            $itemTextSource = $item->localized('description') ?: $item->localized('content');
                            $itemText = filled($itemTextSource) ? str((string) $itemTextSource)->stripTags()->squish()->toString() : null;
                            $imageFirst = $storyLayout === 'media_left' || ($storyLayout === 'path' && $loop->odd);
                        @endphp
                        <article class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1fr)] lg:items-center">
                            <figure @class([
                                'overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/20',
                                'lg:order-2' => ! $imageFirst,
                            ])>
                                @if ($itemImage)
                                    <img src="{{ $itemImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3]" :icon="$item->icon ?: $fallbackIcon" icon-class="size-10" />
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
            @endif

            @if ($this->hasMoreRecords())
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
