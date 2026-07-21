        <?php if ($type === 'about') { ?>
            @php
                $aboutLayout = (string) data_get($section, 'settings.layout_variant', 'split');
                $aboutLayout = in_array($aboutLayout, ['split', 'cover', 'background', 'diagonal', 'curved_image', 'letter', 'editorial_frame'], true) ? $aboutLayout : 'split';
                $aboutItem = $items->first();
                $itemText = $aboutItem ? ($aboutItem->localized('content') ?: $aboutItem->localized('description')) : null;
                $itemStatement = $aboutItem ? $aboutItem->localized('subtitle') : null;
                $itemImage = $aboutItem
                    ? (method_exists($aboutItem, 'imageUrl') ? $aboutItem->imageUrl('xlarge') : data_get($aboutItem, 'image'))
                    : null;
                $legacyText = $section?->localized('content');
                $legacyImage = data_get($section, 'image');
                $sectionText = $itemText ?: $legacyText;
                $sectionStatement = trim((string) $itemStatement);
                $hasAboutCopy = $sectionText || $sectionStatement !== '';
                $sectionImage = $assetUrl($itemImage ?: $legacyImage);
                $aboutImageSlot = $sectionImage || $hasAboutCopy;
                $aboutImageIcon = 'photo';
                $sectionParagraphs = $sectionText ? (preg_split('/\R+/', trim($sectionText)) ?: []) : [];
            @endphp

            @if (! $hasAboutCopy && ! $sectionImage)
                <x-public-section-empty-state
                    :section="$section"
                    class="cx-public-section-content"
                    icon="document-text"
                    :title="__('Sadržaj uskoro')"
                    :description="__('Informacije o nama prikazat će se ovdje kada budu spremne za objavu.')"
                    compact
                />
            @else
                @if ($aboutLayout === 'background')
                    <div class="cx-public-section-content">
                        <div class="relative cx-public-mobile-bleed min-h-[28rem] overflow-hidden bg-zinc-900 px-6 py-12 shadow-sm shadow-zinc-950/10 sm:rounded-xl sm:px-10 sm:py-16 dark:shadow-black/20 lg:min-h-[34rem] lg:px-14">
                            @if ($sectionImage)
                                <img src="{{ $sectionImage }}" alt="" class="absolute inset-0 size-full object-cover" loading="lazy" decoding="async">
                            @else
                                <x-corexis::public-image-placeholder
                                    class="absolute inset-0 size-full"
                                    :icon="$aboutImageIcon"
                                    icon-class="size-14"
                                />
                            @endif

                            <div class="absolute inset-0 bg-zinc-950/0"></div>
                            <div class="absolute inset-0 bg-gradient-to-br from-zinc-950/25 via-transparent to-zinc-950/25"></div>

                            <div class="relative flex min-h-[20rem] items-end lg:min-h-[26rem]">
                                @if ($sectionText)
                                    <div class="w-full cx-public-copy-narrow rounded-xl bg-white/80 p-8 text-zinc-950 shadow-sm shadow-zinc-950/10 ring-1 ring-white/70 backdrop-blur-xl dark:bg-zinc-950/85 dark:text-white dark:ring-white/15 sm:p-10 lg:ml-8 lg:p-12">
                                        <div @class([
                                            'cx-public-stack-loose cx-public-body text-zinc-800 dark:text-zinc-200 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                        ])>
                                            @foreach ($sectionParagraphs as $paragraph)
                                                @if (trim($paragraph) !== '')
                                                    <p>{{ trim($paragraph) }}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($sectionStatement !== '')
                            <div class="cx-public-container-feature cx-public-section-content-compact">
                                @include('niva-template::templates.classic.partials.statement-quote', ['statement' => $sectionStatement, 'widthClass' => 'w-full max-w-none'])
                            </div>
                        @endif
                    </div>
                @elseif ($aboutLayout === 'diagonal')
                    <div class="cx-public-section-content-spacious">
                        <div @class([
                            'grid gap-8 lg:items-stretch',
                            'lg:grid-cols-[minmax(0,0.96fr)_minmax(0,1fr)]' => $aboutImageSlot && $hasAboutCopy,
                        ])>
                            @if ($aboutImageSlot)
                                <figure @class([
                                    'cx-public-mobile-bleed h-72 overflow-hidden bg-zinc-100 [clip-path:polygon(0_0,100%_0,100%_86%,0_100%)] sm:h-96 sm:rounded-xl dark:bg-zinc-900',
                                    'lg:h-auto lg:min-h-[30rem] lg:[clip-path:polygon(0_0,100%_0,88%_100%,0_100%)]' => $hasAboutCopy,
                                    'lg:h-[30rem]' => ! $hasAboutCopy,
                                ])>
                                    @if ($sectionImage)
                                        <img src="{{ $sectionImage }}" alt="" class="size-full object-cover" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="size-full" :icon="$aboutImageIcon" icon-class="size-12" />
                                    @endif
                                </figure>
                            @endif

                            @if ($hasAboutCopy)
                                <div class="flex items-center">
                                    <div class="cx-public-copy-narrow">
                                        @if ($sectionStatement !== '')
                                            @include('niva-template::templates.classic.partials.statement-quote', ['statement' => $sectionStatement])
                                        @endif

                                        @if ($sectionText)
                                            <div @class([
                                                'cx-public-stack-loose cx-public-body text-zinc-700 dark:text-zinc-300 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                                'mt-6' => $sectionStatement !== '',
                                            ])>
                                                @foreach ($sectionParagraphs as $paragraph)
                                                    @if (trim($paragraph) !== '')
                                                        <p>{{ trim($paragraph) }}</p>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif ($aboutLayout === 'curved_image')
                    <div class="cx-public-section-content-spacious grid gap-10 lg:grid-cols-[minmax(0,0.46fr)_minmax(0,0.54fr)] lg:items-center lg:gap-14">
                        @if ($aboutImageSlot)
                            @php($clipId = 'about-image-rounded-notch-'.$section->getKey())
                            <svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false">
                                <clipPath id="{{ $clipId }}" clipPathUnits="objectBoundingBox">
                                    <path d="M0.04 0 H0.96 C0.985 0 1 0.018 0.995 0.045 L0.914 0.445 C0.906 0.475 0.906 0.505 0.914 0.535 L0.995 0.955 C1 0.982 0.985 1 0.96 1 H0.04 C0.018 1 0 0.982 0 0.96 V0.04 C0 0.018 0.018 0 0.04 0 Z" />
                                </clipPath>
                            </svg>
                            <figure class="relative aspect-[4/3] w-full overflow-hidden" style="-webkit-clip-path: url(#{{ $clipId }}); clip-path: url(#{{ $clipId }});">
                                @if ($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="" class="h-full w-full object-cover object-center" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="h-full w-full" :icon="$aboutImageIcon" icon-class="size-12" />
                                @endif
                            </figure>
                        @endif

                        @if ($hasAboutCopy)
                            <div @class([
                                'flex items-center',
                                'lg:col-span-2' => ! $aboutImageSlot,
                            ])>
                                <div class="cx-public-copy-narrow">
                                    @if ($sectionStatement !== '')
                                        @include('niva-template::templates.classic.partials.statement-quote', ['statement' => $sectionStatement])
                                    @endif

                                    @if ($sectionText)
                                        <div @class([
                                            'cx-public-stack-loose cx-public-body text-zinc-700 dark:text-zinc-300 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                            'mt-6' => $sectionStatement !== '',
                                        ])>
                                            @foreach ($sectionParagraphs as $paragraph)
                                                @if (trim($paragraph) !== '')
                                                    <p>{{ trim($paragraph) }}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @elseif ($aboutLayout === 'cover')
                    <div class="cx-public-section-content-spacious">
                        @if ($aboutImageSlot)
                            <figure class="overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/20">
                                @if ($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="" class="max-h-[32rem] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="min-h-[18rem] w-full sm:min-h-[24rem]" :icon="$aboutImageIcon" icon-class="size-12" />
                                @endif
                            </figure>
                        @endif

                        @if ($hasAboutCopy)
                            <div class="cx-public-container-feature cx-public-section-content-compact">
                                @if ($sectionStatement !== '')
                                    @include('niva-template::templates.classic.partials.statement-quote', ['statement' => $sectionStatement, 'widthClass' => 'w-full max-w-none'])
                                @endif

                                @if ($sectionText)
                                    <div @class([
                                        'columns-1 gap-10 cx-public-body text-zinc-700 dark:text-zinc-300 md:columns-2 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                        'mt-6' => $sectionStatement !== '',
                                    ])>
                                        @foreach ($sectionParagraphs as $paragraph)
                                            @if (trim($paragraph) !== '')
                                                <p>{{ trim($paragraph) }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @elseif ($aboutLayout === 'editorial_frame')
                    <div @class([
                        'cx-public-section-content-spacious mx-auto max-w-5xl grid gap-8 lg:items-center lg:gap-10',
                        'lg:grid-cols-[minmax(0,1.15fr)_minmax(20rem,0.85fr)]' => $aboutImageSlot && $sectionText,
                    ])>
                        @if ($aboutImageSlot)
                            <figure class="cx-public-mobile-bleed relative overflow-hidden bg-zinc-900 sm:rounded-xl">
                                @if ($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$aboutImageIcon" icon-class="size-12" />
                                @endif

                                @if ($sectionStatement !== '')
                                    <div class="absolute inset-x-0 bottom-0 bg-zinc-950/75 px-5 py-4 backdrop-blur-[2px] sm:px-6 sm:py-5">
                                        <p class="max-w-xl border-l-2 border-[color:var(--niva-primary-200)] pl-4 text-lg font-medium leading-relaxed tracking-tight text-white sm:text-xl">{{ $sectionStatement }}</p>
                                    </div>
                                @endif
                            </figure>
                        @endif

                        @if ($sectionText)
                            <div @class([
                                'border-t border-zinc-200 pt-6 cx-public-body text-zinc-700 dark:border-zinc-800 dark:text-zinc-300 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                'lg:col-span-2' => ! $aboutImageSlot,
                            ])>
                                @foreach ($sectionParagraphs as $paragraph)
                                    @if (trim($paragraph) !== '')
                                        <p>{{ trim($paragraph) }}</p>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if (! $aboutImageSlot && $sectionStatement !== '')
                            <div class="border-l-2 border-[color:var(--niva-primary-300)] pl-5 sm:pl-6">
                                <p class="cx-public-quote-featured whitespace-pre-line text-zinc-950 dark:text-zinc-100">{{ $sectionStatement }}</p>
                            </div>
                        @endif
                    </div>
                @elseif ($aboutLayout === 'letter')
                    <div class="cx-public-section-content-spacious grid gap-8 lg:grid-cols-[minmax(16rem,0.88fr)_minmax(0,1.12fr)] lg:items-center lg:gap-12">
                        @if ($aboutImageSlot)
                            <figure class="cx-public-media-frame-surface">
                                @if ($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$aboutImageIcon" icon-class="size-10" />
                                @endif
                            </figure>
                        @endif

                        @if ($hasAboutCopy)
                            <div @class([
                                'min-w-0 cx-public-copy-narrow lg:max-w-none',
                                'lg:col-span-2' => ! $aboutImageSlot,
                            ])>
                                @if ($sectionStatement !== '')
                                    @include('niva-template::templates.classic.partials.statement-quote', [
                                        'statement' => $sectionStatement,
                                        'widthClass' => 'w-full max-w-none',
                                        'textRoleClass' => 'cx-public-quote-featured',
                                        'textClass' => 'text-zinc-950 dark:text-zinc-100',
                                        'quoteClass' => 'rounded-xl border-s-4 bg-[color:var(--niva-primary-50)]/70 px-5 py-4 shadow-none ring-1 ring-[color:var(--niva-primary-100)]/70 dark:bg-[color:var(--niva-primary-950)]/35 dark:ring-[color:var(--niva-primary-900)]/50',
                                        'showQuoteMark' => false,
                                    ])
                                @endif

                                @if ($sectionText)
                                    <div @class([
                                        'cx-public-stack-loose cx-public-body text-zinc-700 dark:text-zinc-300 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                        'mt-6' => $sectionStatement !== '',
                                    ])>
                                        @foreach ($sectionParagraphs as $paragraph)
                                            @if (trim($paragraph) !== '')
                                                <p>{{ trim($paragraph) }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="cx-public-section-content-spacious grid gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(20rem,0.86fr)] lg:items-center">
                        @if ($hasAboutCopy)
                            <div class="cx-public-copy">
                                @if ($sectionStatement !== '')
                                    @include('niva-template::templates.classic.partials.statement-quote', ['statement' => $sectionStatement])
                                @endif

                                @if ($sectionText)
                                    <div @class([
                                        'cx-public-stack-loose cx-public-body text-zinc-700 dark:text-zinc-300 [&_p]:mb-5 [&_p:last-child]:mb-0',
                                        'mt-6' => $sectionStatement !== '',
                                    ])>
                                        @foreach ($sectionParagraphs as $paragraph)
                                            @if (trim($paragraph) !== '')
                                                <p>{{ trim($paragraph) }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if ($aboutImageSlot)
                            <figure class="relative overflow-hidden rounded-xl bg-zinc-100 shadow-sm shadow-zinc-950/10 dark:bg-zinc-900 dark:shadow-black/25">
                                @if ($sectionImage)
                                    <img src="{{ $sectionImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$aboutImageIcon" icon-class="size-10" />
                                @endif
                            </figure>
                        @endif
                    </div>
                @endif
            @endif
        <?php } ?>
