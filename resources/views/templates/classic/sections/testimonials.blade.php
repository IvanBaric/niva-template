        <?php if ($type === 'testimonials') { ?>
            @php
                $testimonialLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $testimonialLayout = in_array($testimonialLayout, ['cards', 'portraits', 'quotes', 'split_grid', 'spotlight', 'notes', 'carousel', 'featured_mosaic', 'masonry_columns'], true) ? $testimonialLayout : 'cards';
                $testimonialCarouselName = 'testimonials-carousel-'.(string) data_get($section, 'uuid', data_get($section, 'id', 'section'));
                $testimonialCarouselHasMore = $testimonialLayout === 'carousel' && $this->hasMoreCarouselItems('testimonials');
                $testimonialCarouselItems = $items;
                $testimonialTitleFor = fn ($item) => is_array($item) ? data_get($item, 'title') : $item->localized('title');
                $testimonialSubtitleFor = fn ($item) => is_array($item) ? data_get($item, 'subtitle') : $item->localized('subtitle');
                $testimonialTextFor = fn ($item) => filled((is_array($item) ? (data_get($item, 'content') ?: data_get($item, 'description')) : ($item->localized('content') ?: $item->localized('description'))))
                    ? str((string) (is_array($item) ? (data_get($item, 'content') ?: data_get($item, 'description')) : ($item->localized('content') ?: $item->localized('description'))))->stripTags()->squish()->toString()
                    : null;
                $testimonialImageFor = fn ($item) => is_array($item)
                    ? $assetUrl(data_get($item, 'image'))
                    : (method_exists($item, 'imageUrl') ? $item->imageUrl('thumb') : $assetUrl(data_get($item, 'image')));
            @endphp

            @if ($testimonialLayout === 'carousel')
                @if ($testimonialCarouselItems->isEmpty())
                    <x-public-section-empty-state
                        :section="$section"
                        class="cx-public-section-content"
                        icon="chat-bubble-left-right"
                        :title="__('Izjave će se prikazati ovdje.')"
                        :description="__('Kada budu objavljene, pojavit će se u ovoj sekciji.')"
                        compact
                    />
                @else
                    <div class="mt-6">
                        @if ($testimonialCarouselItems->count() > 1 || $testimonialCarouselHasMore)
                            @include('niva-template::templates.classic.partials.carousel-controls', [
                                'name' => $testimonialCarouselName,
                                'loadTarget' => 'testimonials',
                                'hasMore' => $testimonialCarouselHasMore,
                            ])
                        @endif

                        <flux:carousel name="{{ $testimonialCarouselName }}" class="-mx-4" :arrows="false" fade advance="page" track:class="px-4 scroll-px-4">
                            @foreach ($testimonialCarouselItems as $item)
                                @php
                                    $testimonialImage = $testimonialImageFor($item);
                                    $testimonialText = $testimonialTextFor($item);
                                @endphp
                                <flux:carousel.slide class="w-[85%] sm:w-1/2 lg:w-1/3" wire:key="testimonials-carousel-{{ data_get($item, 'id', $loop->index) }}">
                                    <article class="flex min-h-52 flex-col cx-public-surface px-5 py-5 dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 sm:px-6">
                                        @if ($testimonialText)
                                            <blockquote class="cx-public-quote-compact text-zinc-700 dark:text-zinc-300">
                                                &ldquo;{{ $testimonialText }}&rdquo;
                                            </blockquote>
                                        @endif

                                        <div class="mt-5 flex items-center gap-3">
                                            <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                                @if ($testimonialImage)
                                                    <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                                @else
                                                    <flux:icon name="sparkles" class="size-4" />
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $testimonialTitleFor($item) }}</h3>
                                                @if ($testimonialSubtitleFor($item))
                                                    <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $testimonialSubtitleFor($item) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                </flux:carousel.slide>
                            @endforeach
                        </flux:carousel>
                    </div>
                @endif
            @elseif ($testimonialLayout === 'featured_mosaic' && $items->isNotEmpty())
                @php
                    $featuredTestimonial = $items->first();
                    $featuredText = $testimonialTextFor($featuredTestimonial);
                    $featuredImage = $testimonialImageFor($featuredTestimonial);
                    $featuredInitial = str((string) $testimonialTitleFor($featuredTestimonial))->trim()->substr(0, 1)->upper()->toString();
                    $sideTestimonials = $items->skip(1)->take(2);
                    $lowerTestimonials = $items->skip(3);
                @endphp

                <div class="cx-public-section-content mx-auto max-w-6xl">
                    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(18rem,0.85fr)] lg:items-start">
                        <figure wire:key="testimonials-featured-mosaic-main-{{ data_get($featuredTestimonial, 'id', 'first') }}" class="cx-public-surface-plain cx-public-card-padding-loose dark:bg-zinc-950 dark:shadow-black/20 sm:p-8">
                            @if ($featuredText)
                                <blockquote class="cx-public-quote-featured text-zinc-900 dark:text-white">
                                    &ldquo;{{ $featuredText }}&rdquo;
                                </blockquote>
                            @endif

                            <figcaption class="mt-8 flex flex-wrap items-center gap-4">
                                <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                    @if ($featuredImage)
                                        <img src="{{ $featuredImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <span class="cx-public-avatar-initial">{{ $featuredInitial }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $testimonialTitleFor($featuredTestimonial) }}</h3>
                                    @if ($testimonialSubtitleFor($featuredTestimonial))
                                        <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $testimonialSubtitleFor($featuredTestimonial) }}</p>
                                    @endif
                                </div>
                            </figcaption>
                        </figure>

                        @if ($sideTestimonials->isNotEmpty())
                            <div class="grid gap-6">
                                @foreach ($sideTestimonials as $item)
                                    @php
                                        $testimonialImage = $testimonialImageFor($item);
                                        $testimonialText = $testimonialTextFor($item);
                                        $testimonialInitial = str((string) $testimonialTitleFor($item))->trim()->substr(0, 1)->upper()->toString();
                                    @endphp
                                    <figure wire:key="testimonials-featured-mosaic-side-{{ data_get($item, 'id', $loop->index) }}" class="cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                                        @if ($testimonialText)
                                            <blockquote class="cx-public-item-text text-zinc-700 dark:text-zinc-300">
                                                &ldquo;{{ $testimonialText }}&rdquo;
                                            </blockquote>
                                        @endif

                                        <figcaption class="mt-5 flex items-center gap-3">
                                            <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                                @if ($testimonialImage)
                                                    <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                                @else
                                                    <span class="cx-public-avatar-initial">{{ $testimonialInitial }}</span>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $testimonialTitleFor($item) }}</h3>
                                                @if ($testimonialSubtitleFor($item))
                                                    <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $testimonialSubtitleFor($item) }}</p>
                                                @endif
                                            </div>
                                        </figcaption>
                                    </figure>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if ($lowerTestimonials->isNotEmpty())
                        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($lowerTestimonials as $item)
                                @php
                                    $testimonialImage = $testimonialImageFor($item);
                                    $testimonialText = $testimonialTextFor($item);
                                    $testimonialInitial = str((string) $testimonialTitleFor($item))->trim()->substr(0, 1)->upper()->toString();
                                @endphp
                                <figure wire:key="testimonials-featured-mosaic-more-{{ data_get($item, 'id', $loop->index) }}" class="cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                                    @if ($testimonialText)
                                        <blockquote class="cx-public-item-text text-zinc-700 dark:text-zinc-300">
                                            &ldquo;{{ $testimonialText }}&rdquo;
                                        </blockquote>
                                    @endif

                                    <figcaption class="mt-5 flex items-center gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                            @if ($testimonialImage)
                                                <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                            @else
                                                <span class="cx-public-avatar-initial">{{ $testimonialInitial }}</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $testimonialTitleFor($item) }}</h3>
                                            @if ($testimonialSubtitleFor($item))
                                                <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $testimonialSubtitleFor($item) }}</p>
                                            @endif
                                        </div>
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @endif
                </div>
            @elseif ($testimonialLayout === 'masonry_columns')
                <div class="cx-public-section-content mx-auto max-w-6xl">
                    <div class="grid gap-8 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($items as $item)
                            @php
                                $testimonialImage = $testimonialImageFor($item);
                                $testimonialText = $testimonialTextFor($item);
                                $testimonialInitial = str((string) $testimonialTitleFor($item))->trim()->substr(0, 1)->upper()->toString();
                            @endphp
                            <figure wire:key="testimonials-masonry-columns-{{ data_get($item, 'id', $loop->index) }}" class="flex h-full flex-col justify-between p-6 text-left">
                                @if ($testimonialText)
                                    <blockquote class="cx-public-item-text text-zinc-700 dark:text-zinc-300">
                                        &ldquo;{{ $testimonialText }}&rdquo;
                                    </blockquote>
                                @endif

                                <figcaption class="mt-6 flex items-center gap-3">
                                    <div class="flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white text-[color:var(--niva-primary-700)] ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-[color:var(--niva-primary-300)] dark:ring-zinc-800">
                                        @if ($testimonialImage)
                                            <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                        @else
                                            <span class="cx-public-avatar-initial">{{ $testimonialInitial }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $testimonialTitleFor($item) }}</h3>
                                        @if ($testimonialSubtitleFor($item))
                                            <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $testimonialSubtitleFor($item) }}</p>
                                        @endif
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            @elseif ($testimonialLayout === 'portraits')
                <div class="cx-public-section-content">
                    <div class="cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($items as $item)
                            @php
                                $testimonialImage = $testimonialImageFor($item);
                                $testimonialText = $testimonialTextFor($item);
                            @endphp
                            <article class="flex min-h-72 flex-col items-center cx-public-surface-plain px-7 py-8 text-center">
                                @if ($testimonialImage)
                                    <img src="{{ $testimonialImage }}" alt="" class="mx-auto size-16 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-800" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="mx-auto size-16 rounded-full ring-1 ring-[color:var(--niva-primary-100)] dark:ring-[color:var(--niva-primary-900)]" icon="sparkles" icon-class="size-6" />
                                @endif

                                @if ($testimonialText)
                                    <blockquote class="mx-auto mt-6 max-w-sm flex-1 cx-public-quote-compact text-zinc-800 dark:text-zinc-200">
                                        &ldquo;{{ $testimonialText }}&rdquo;
                                    </blockquote>
                                @endif

                                <div class="mt-6">
                                    <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                    @if ($item->localized('subtitle'))
                                        <p class="mt-1 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($testimonialLayout === 'quotes')
                <div class="cx-public-section-content">
                    <div class="cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($items as $item)
                            @php
                                $testimonialImage = $testimonialImageFor($item);
                                $testimonialText = $testimonialTextFor($item);
                            @endphp
                            <article class="flex min-h-72 flex-col cx-public-surface-plain px-7 py-8 dark:bg-zinc-950 dark:shadow-black/20">
                                <span class="cx-public-quote-mark text-[color:var(--niva-primary-600)] dark:text-[color:var(--niva-primary-300)]" aria-hidden="true">&ldquo;</span>

                                @if ($testimonialText)
                                    <blockquote class="mt-4 flex-1 cx-public-quote-medium text-zinc-800 dark:text-zinc-200">
                                        &ldquo;{{ $testimonialText }}&rdquo;
                                    </blockquote>
                                @endif

                                <div class="mt-6 flex items-center gap-3">
                                    @if ($testimonialImage)
                                        <img src="{{ $testimonialImage }}" alt="" class="size-12 rounded-full object-cover ring-1 ring-zinc-200 dark:ring-zinc-800" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="size-12 shrink-0 rounded-full ring-1 ring-[color:var(--niva-primary-100)] dark:ring-[color:var(--niva-primary-900)]" icon="sparkles" icon-class="size-5" />
                                    @endif
                                    <div class="min-w-0">
                                        <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                        @if ($item->localized('subtitle'))
                                            <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($testimonialLayout === 'split_grid')
                <div class="cx-public-section-content">
                    <div class="cx-public-grid md:grid-cols-2">
                        @foreach ($items as $item)
                            @php
                                $testimonialImage = $testimonialImageFor($item);
                                $testimonialText = $testimonialTextFor($item);
                                $testimonialInitial = str($item->localized('title'))->trim()->substr(0, 1)->upper()->toString();
                            @endphp
                            <article @class([
                                'cx-public-grid cx-public-surface-plain px-6 py-8 dark:bg-zinc-950 dark:shadow-black/20 sm:grid-cols-[5rem_minmax(0,1fr)] sm:items-center lg:px-10 lg:py-10',
                                'md:col-span-2' => $loop->last && $loop->count % 2 === 1,
                            ])>
                                <div class="flex size-20 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    @if ($testimonialImage)
                                        <img src="{{ $testimonialImage }}" alt="" class="size-14 rounded-full object-cover ring-1 ring-white dark:ring-zinc-800" loading="lazy" decoding="async">
                                    @else
                                        <span class="cx-public-avatar-initial">{{ $testimonialInitial }}</span>
                                    @endif
                                </div>

                                <div class="min-w-0">
                                    @if ($testimonialText)
                                        <blockquote class="cx-public-quote text-zinc-800 dark:text-zinc-100">
                                            &ldquo;{{ $testimonialText }}&rdquo;
                                        </blockquote>
                                    @endif
                                    <div class="mt-4">
                                        <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                        @if ($item->localized('subtitle'))
                                            <p class="mt-1 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($testimonialLayout === 'spotlight' && $items->isNotEmpty())
                @php
                    $featuredTestimonial = $items->first();
                    $featuredText = $testimonialTextFor($featuredTestimonial);
                    $featuredImage = $testimonialImageFor($featuredTestimonial);
                    $secondaryTestimonials = $items->skip(1);
                @endphp
                <div class="cx-public-section-content cx-public-grid-loose lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:items-start">
                    <article class="cx-public-surface-plain cx-public-card-padding-loose dark:bg-zinc-950 dark:shadow-black/20 sm:p-8">
                        <div class="flex items-center gap-4">
                            <div class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                @if ($featuredImage)
                                    <img src="{{ $featuredImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <flux:icon name="sparkles" class="size-7" />
                                @endif
                            </div>
                            <div class="min-w-0">
                                <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $featuredTestimonial->localized('title') }}</h3>
                                @if ($featuredTestimonial->localized('subtitle'))
                                    <p class="mt-1 cx-public-meta text-zinc-500 dark:text-zinc-400">{{ $featuredTestimonial->localized('subtitle') }}</p>
                                @endif
                            </div>
                        </div>

                        @if ($featuredText)
                            <blockquote class="mt-7 cx-public-quote-featured text-zinc-900 dark:text-white">
                                &ldquo;{{ $featuredText }}&rdquo;
                            </blockquote>
                        @endif
                    </article>

                    @if ($secondaryTestimonials->isNotEmpty())
                        <div class="cx-public-grid-compact">
                            @foreach ($secondaryTestimonials as $item)
                                @php
                                    $testimonialImage = $testimonialImageFor($item);
                                    $testimonialText = $testimonialTextFor($item);
                                @endphp
                                <article class="cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                                    @if ($testimonialText)
                                        <blockquote class="cx-public-item-text text-zinc-700 dark:text-zinc-300">
                                            &ldquo;{{ $testimonialText }}&rdquo;
                                        </blockquote>
                                    @endif
                                    <div class="mt-5 flex items-center gap-3">
                                        <div class="flex size-11 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                            @if ($testimonialImage)
                                                <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                            @else
                                                <flux:icon name="sparkles" class="size-5" />
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                            @if ($item->localized('subtitle'))
                                                <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @elseif ($testimonialLayout === 'notes')
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($items as $item)
                            @php
                                $testimonialImage = $testimonialImageFor($item);
                                $testimonialText = $testimonialTextFor($item);
                                $noteClass = match ($loop->iteration % 3) {
                                    1 => 'lg:-rotate-1',
                                    2 => 'lg:rotate-1',
                                    default => 'lg:rotate-0',
                                };
                            @endphp
                            <article class="{{ $noteClass }} flex h-full flex-col cx-public-surface-plain cx-public-card-padding cx-public-card-hover hover:rotate-0 dark:bg-zinc-950 dark:shadow-black/20">
                                @if ($testimonialText)
                                    <blockquote class="flex-1 cx-public-quote text-zinc-700 dark:text-zinc-200">
                                        &ldquo;{{ $testimonialText }}&rdquo;
                                    </blockquote>
                                @endif
                                <div class="mt-6 flex items-center gap-3">
                                    <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-white text-[color:var(--niva-primary-700)] shadow-sm ring-1 ring-zinc-200/70 dark:bg-zinc-900 dark:text-[color:var(--niva-primary-300)] dark:ring-zinc-800">
                                        @if ($testimonialImage)
                                            <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                        @else
                                            <flux:icon name="sparkles" class="size-5" />
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                        @if ($item->localized('subtitle'))
                                            <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="cx-public-section-content cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($items as $item)
                        @php
                            $testimonialImage = $testimonialImageFor($item);
                            $testimonialText = $testimonialTextFor($item);
                        @endphp
                        <article class="flex h-full flex-col cx-public-surface-plain cx-public-card-padding cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                            @if ($testimonialText)
                                <blockquote class="flex-1 cx-public-quote text-zinc-700 dark:text-zinc-300">
                                    &ldquo;{{ $testimonialText }}&rdquo;
                                </blockquote>
                            @endif
                            <div class="mt-6 flex items-center gap-3">
                                <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                    @if ($testimonialImage)
                                        <img src="{{ $testimonialImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <flux:icon name="sparkles" class="size-5" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                    @if ($item->localized('subtitle'))
                                        <p class="mt-0.5 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $item->localized('subtitle') }}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        <?php } ?>
