        <?php if ($type === 'statistics') { ?>
            @php
                $statsLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $statsLayout = in_array($statsLayout, ['cards', 'story', 'ribbon', 'split_grid', 'compact_grid'], true) ? $statsLayout : 'cards';
            @endphp

            @if ($statsLayout === 'story')
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($items as $item)
                            <article class="flex min-h-44 flex-col cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                                <div class="flex items-center justify-between gap-4">
                                    <p class="cx-public-stat-value text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->setting('value', $item->localized('title')) }}{{ $item->setting('suffix') }}</p>
                                    @if ($item->icon)
                                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                            <flux:icon :name="$item->icon" class="size-5" />
                                        </span>
                                    @endif
                                </div>
                                <h3 class="mt-5 cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                @if ($item->localized('description') || $item->localized('content'))
                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($statsLayout === 'ribbon')
                <div class="cx-public-section-content cx-public-grid-tight sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($items as $item)
                        <article class="flex items-center gap-4 cx-public-surface-plain px-5 py-4 dark:bg-zinc-950 dark:shadow-black/20">
                            @if ($item->icon)
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon :name="$item->icon" class="size-5" />
                                </span>
                            @endif
                            <div class="min-w-0">
                                <p class="cx-public-stat-value-compact text-zinc-950 dark:text-white">{{ $item->setting('value', $item->localized('title')) }}{{ $item->setting('suffix') }}</p>
                                <p class="mt-1 cx-public-meta text-zinc-600 dark:text-zinc-300">{{ $item->localized('title') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif ($statsLayout === 'split_grid')
                <div class="cx-public-section-content">
                    <div class="cx-public-grid md:grid-cols-2">
                        @foreach ($items as $item)
                            @php
                                $statDescription = $item->localized('description') ?: $item->localized('content');
                                $statInitial = str($item->localized('title'))->trim()->substr(0, 1)->upper()->toString();
                            @endphp
                            <article @class([
                                'cx-public-grid cx-public-surface-plain px-6 py-8 dark:bg-zinc-950 dark:shadow-black/20 sm:grid-cols-[5rem_minmax(0,1fr)] sm:items-center lg:px-10 lg:py-10',
                                'md:col-span-2' => $loop->last && $loop->count % 2 === 1,
                            ])>
                                <div class="flex size-20 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    @if ($item->icon)
                                        <flux:icon :name="$item->icon" class="size-7" />
                                    @else
                                        <span class="cx-public-avatar-initial">{{ $statInitial }}</span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <p class="cx-public-stat-value text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->setting('value', $item->localized('title')) }}{{ $item->setting('suffix') }}</p>
                                    <h3 class="mt-2 cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                    @if ($statDescription)
                                        <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $statDescription }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($statsLayout === 'compact_grid')
                <dl class="cx-public-section-content grid grid-cols-1 gap-y-8 text-center sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-4">
                    @foreach ($items as $item)
                        <div class="flex min-h-36 flex-col justify-center p-8">
                            <dt class="mt-3 flex items-center justify-center gap-2 cx-public-meta-strong text-zinc-600 dark:text-zinc-300">
                                @if ($item->icon)
                                    <flux:icon :name="$item->icon" class="size-4 shrink-0 text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]" />
                                @endif
                                <span>{{ $item->localized('title') }}</span>
                            </dt>
                            <dd class="order-first cx-public-stat-value-medium text-zinc-950 dark:text-white">{{ $item->setting('value', $item->localized('title')) }}{{ $item->setting('suffix') }}</dd>
                        </div>
                    @endforeach
                </dl>
            @else
                <div class="cx-public-section-content cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($items as $item)
                        <article class="flex min-h-36 flex-col cx-public-surface-plain cx-public-card-padding cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                            <div class="flex items-center gap-3">
                                @if ($item->icon)
                                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                        <flux:icon :name="$item->icon" class="size-5" />
                                    </span>
                                @endif
                                <p class="cx-public-stat-value-medium text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $item->setting('value', $item->localized('title')) }}{{ $item->setting('suffix') }}</p>
                            </div>
                            <h3 class="mt-4 cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                            @if ($item->localized('description') || $item->localized('content'))
                                <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        <?php } ?>
