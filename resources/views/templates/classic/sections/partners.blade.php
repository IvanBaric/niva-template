        <?php if ($type === 'partners') { ?>
            @php
                $partnersLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $partnersLayout = in_array($partnersLayout, ['cards', 'logos', 'list', 'featured_list'], true) ? $partnersLayout : 'cards';
                $partnerImageFor = fn ($item) => method_exists($item, 'imageUrl')
                    ? $item->imageUrl('thumb')
                    : $assetUrl(data_get($item, 'image'));
            @endphp

            @if ($partnersLayout === 'logos')
                <div class="cx-public-section-content cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($items as $item)
                        @php
                            $partnerImage = $partnerImageFor($item);
                            $partnerDescription = $item->localized('description') ?: $item->localized('content');
                            $partnerUrl = $publicUrl->sanitize($item->url);
                        @endphp
                        <a href="{{ $partnerUrl ?: '#' }}" title="{{ $item->localized('title') }}" aria-label="{{ $item->localized('title') }}" @class([
                            'group flex min-h-36 cursor-pointer flex-col items-center justify-center cx-public-surface-plain cx-public-card-padding text-center cx-public-card-hover',
                            'pointer-events-none' => ! $partnerUrl,
                        ]) @if ($partnerUrl) target="_blank" rel="noopener noreferrer" @endif>
                            <div class="flex h-20 w-full items-center justify-center overflow-hidden rounded-xl bg-zinc-50 cx-public-border dark:bg-zinc-900">
                                @if ($partnerImage)
                                    <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="size-full" icon="heart" icon-class="size-5" />
                                @endif
                            </div>
                            <h3 class="mt-4 cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                            @if ($partnerDescription)
                                <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $partnerDescription }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @elseif ($partnersLayout === 'list')
                <div class="cx-public-section-content cx-public-stack">
                    @foreach ($items as $item)
                        @php
                            $partnerImage = $partnerImageFor($item);
                            $partnerDescription = $item->localized('description') ?: $item->localized('content');
                            $partnerUrl = $publicUrl->sanitize($item->url);
                        @endphp
                        <article class="cx-public-grid-compact cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20 sm:grid-cols-[8rem_1fr_auto] sm:items-center">
                            <div class="flex h-20 items-center justify-center overflow-hidden rounded-xl bg-zinc-50 cx-public-border dark:bg-zinc-900">
                                @if ($partnerImage)
                                    @if ($partnerUrl)
                                        <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" class="flex size-full cursor-pointer items-center justify-center" title="{{ $item->localized('title') }}" aria-label="{{ $item->localized('title') }}">
                                            <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                        </a>
                                    @else
                                        <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                    @endif
                                @else
                                    <x-corexis::public-image-placeholder class="size-full" icon="heart" icon-class="size-6" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                @if ($partnerDescription)
                                    <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $partnerDescription }}</p>
                                @endif
                            </div>
                            @if ($partnerUrl)
                                <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                    {{ __('Posjeti stranicu') }}
                                </a>
                            @endif
                        </article>
                    @endforeach
                </div>
            @elseif ($partnersLayout === 'featured_list')
                <div class="cx-public-section-content cx-public-grid-compact lg:grid-cols-2">
                    @foreach ($items as $item)
                        @php
                            $partnerImage = $partnerImageFor($item);
                            $partnerDescription = $item->localized('description') ?: $item->localized('content');
                            $partnerUrl = $publicUrl->sanitize($item->url);
                        @endphp
                        <article @class([
                            'cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20',
                            'lg:col-span-2' => $loop->first,
                        ])>
                            <div @class([
                                'cx-public-grid sm:items-center',
                                'sm:grid-cols-[7rem_minmax(0,1fr)_auto]' => $loop->first,
                                'sm:grid-cols-[5rem_minmax(0,1fr)]' => ! $loop->first,
                            ])>
                                <div @class([
                                    'flex shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-50 cx-public-border dark:bg-zinc-900',
                                    'h-24 w-28' => $loop->first,
                                    'size-20' => ! $loop->first,
                                ])>
                                    @if ($partnerImage)
                                        @if ($partnerUrl)
                                            <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" class="flex size-full cursor-pointer items-center justify-center" title="{{ $item->localized('title') }}" aria-label="{{ $item->localized('title') }}">
                                                <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="size-full" icon="heart" icon-class="size-5" />
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                    @if ($partnerDescription)
                                        <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $partnerDescription }}</p>
                                    @endif
                                </div>

                                @if ($partnerUrl)
                                    <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" @class([
                                        'inline-flex cursor-pointer items-center gap-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] transition dark:text-[color:var(--niva-primary-300)]',
                                        'sm:justify-self-end' => $loop->first,
                                        'sm:col-start-2 sm:justify-self-end' => ! $loop->first,
                                    ])>
                                        {{ __('Posjeti stranicu') }}
                                        <flux:icon name="arrow-right" class="size-4" />
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="cx-public-section-content cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $partnerImage = $partnerImageFor($item);
                            $partnerDescription = $item->localized('description') ?: $item->localized('content');
                            $partnerUrl = $publicUrl->sanitize($item->url);
                        @endphp
                        <article class="flex h-full flex-col cx-public-surface-plain cx-public-card-padding text-left cx-public-card-hover">
                            <div class="flex items-center gap-4">
                                <div class="flex h-16 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-zinc-50 cx-public-border dark:bg-zinc-900">
                                    @if ($partnerImage)
                                        @if ($partnerUrl)
                                            <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" class="flex size-full cursor-pointer items-center justify-center" title="{{ $item->localized('title') }}" aria-label="{{ $item->localized('title') }}">
                                                <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $partnerImage }}" alt="" class="max-h-full max-w-full object-contain" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="size-full" icon="heart" icon-class="size-5" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-1 flex-col">
                                @if ($partnerDescription)
                                    <p class="flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $partnerDescription }}</p>
                                @endif
                                @if ($partnerUrl)
                                    <a href="{{ $partnerUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                        {{ __('Posjeti stranicu') }}
                                    </a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        <?php } ?>
