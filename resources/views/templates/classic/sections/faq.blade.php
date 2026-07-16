        <?php if ($type === 'faq') { ?>
            @php
                $faqLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $faqLayout = in_array($faqLayout, ['cards', 'accordion', 'notebook', 'compact_accordion', 'answer_grid', 'expanded_accordion', 'timeline_accordion'], true) ? $faqLayout : 'cards';
                $faqAnswerFor = fn ($item) => $item->localized('content') ?: $item->localized('description');
            @endphp

            @if ($faqLayout === 'compact_accordion')
                <div x-data="{ open: null }" class="cx-public-section-content cx-public-stack-compact">
                    @foreach ($items as $item)
                        @php
                            $faqAnswer = $faqAnswerFor($item);
                        @endphp
                        <article class="cx-public-surface-plain dark:bg-zinc-950 dark:shadow-black/20">
                            <button type="button" class="cursor-pointer grid w-full grid-cols-[2rem_minmax(0,1fr)_1.25rem] items-center gap-4 px-5 py-4 text-left transition hover:bg-zinc-50 dark:hover:bg-zinc-900/60" @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="open === {{ $loop->index }} ? 'true' : 'false'">
                                <span class="inline-flex size-7 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon name="question-mark-circle" class="size-3.5" />
                                </span>
                                <span class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</span>
                                <flux:icon name="chevron-down" class="size-4 text-[color:var(--niva-primary-700)] cx-public-motion-icon dark:text-[color:var(--niva-primary-300)]" x-bind:class="open === {{ $loop->index }} ? 'rotate-180' : ''" />
                            </button>
                            @if ($faqAnswer)
                                <div x-show="open === {{ $loop->index }}" x-transition.opacity.duration.150ms class="px-5 pb-5 pl-16">
                                    <p class="cx-public-body text-zinc-600 dark:text-zinc-300">{{ $faqAnswer }}</p>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @elseif ($faqLayout === 'answer_grid')
                <div class="cx-public-section-content cx-public-grid-compact md:grid-cols-2">
                    @foreach ($items as $item)
                        @php
                            $faqAnswer = $faqAnswerFor($item);
                        @endphp
                        <article class="cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon name="question-mark-circle" class="size-3.5" />
                                </span>
                                <div>
                                    <h3 class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                    @if ($faqAnswer)
                                        <p class="mt-2 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $faqAnswer }}</p>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @elseif ($faqLayout === 'expanded_accordion')
                <div x-data="{ open: 0 }" class="cx-public-section-content cx-public-stack-compact">
                    @foreach ($items as $item)
                        @php
                            $faqAnswer = $faqAnswerFor($item);
                        @endphp
                        <article class="cx-public-surface-plain dark:bg-zinc-950 dark:shadow-black/20">
                            <button type="button" class="cursor-pointer grid w-full grid-cols-[2rem_minmax(0,1fr)_1.25rem] items-center gap-4 px-5 py-4 text-left transition hover:bg-zinc-50 dark:hover:bg-zinc-900/60" @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="open === {{ $loop->index }} ? 'true' : 'false'">
                                <span class="inline-flex size-7 items-center justify-center rounded-full cx-public-meta-strong transition" x-bind:class="open === {{ $loop->index }} ? 'bg-[color:var(--niva-primary-700)] text-white dark:bg-[color:var(--niva-primary-300)] dark:text-zinc-950' : 'bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]'">
                                    <span x-text="open === {{ $loop->index }} ? '-' : '+'"></span>
                                </span>
                                <span class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</span>
                                <flux:icon name="chevron-down" class="size-4 text-[color:var(--niva-primary-700)] cx-public-motion-icon dark:text-[color:var(--niva-primary-300)]" x-bind:class="open === {{ $loop->index }} ? 'rotate-180' : ''" />
                            </button>
                            @if ($faqAnswer)
                                <div x-show="open === {{ $loop->index }}" x-transition.opacity.duration.150ms class="px-5 pb-5 pl-16">
                                    <p class="cx-public-body text-zinc-600 dark:text-zinc-300">{{ $faqAnswer }}</p>
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @elseif ($faqLayout === 'timeline_accordion')
                <div x-data="{ open: 0 }" class="relative cx-public-section-content cx-public-copy pl-9">
                    <span class="absolute left-3 top-0 h-full w-px bg-[color:var(--niva-primary-600)] dark:bg-[color:var(--niva-primary-300)]/45" aria-hidden="true"></span>
                    <div class="cx-public-stack-compact">
                        @foreach ($items as $item)
                            @php
                                $faqAnswer = $faqAnswerFor($item);
                            @endphp
                            <article class="relative cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20">
                                <span class="absolute -left-[2.05rem] top-5 size-4 rounded-full border border-[color:var(--niva-primary-700)] bg-white transition dark:border-[color:var(--niva-primary-300)] dark:bg-zinc-950" x-bind:class="open === {{ $loop->index }} ? '!bg-[color:var(--niva-primary-700)] dark:!bg-[color:var(--niva-primary-300)]' : ''" aria-hidden="true"></span>
                                <button type="button" class="cursor-pointer grid w-full grid-cols-[minmax(0,1fr)_1.25rem] items-start gap-4 text-left" @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}" :aria-expanded="open === {{ $loop->index }} ? 'true' : 'false'">
                                    <span class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</span>
                                    <flux:icon name="chevron-down" class="mt-1 size-4 text-[color:var(--niva-primary-700)] cx-public-motion-icon dark:text-[color:var(--niva-primary-300)]" x-bind:class="open === {{ $loop->index }} ? 'rotate-180' : ''" />
                                </button>
                                @if ($faqAnswer)
                                    <div x-show="open === {{ $loop->index }}" x-transition.opacity.duration.150ms class="pt-3 pr-8">
                                        <p class="cx-public-body text-zinc-600 dark:text-zinc-300">{{ $faqAnswer }}</p>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            @elseif ($faqLayout === 'accordion')
                <div class="cx-public-section-content cx-public-stack-compact">
                    @foreach ($items as $item)
                        <article class="cx-public-grid-tight cx-public-surface-plain cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20 sm:grid-cols-[minmax(0,0.72fr)_minmax(0,1fr)] sm:gap-8">
                            <h3 class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                            <p class="cx-public-body text-zinc-600 dark:text-zinc-300">{{ $item->localized('content') ?: $item->localized('description') }}</p>
                        </article>
                    @endforeach
                </div>
            @elseif ($faqLayout === 'notebook')
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid md:grid-cols-2">
                        @foreach ($items as $item)
                            @php
                                $noteClass = $loop->odd ? 'lg:-rotate-1' : 'lg:rotate-1';
                            @endphp
                            <article class="{{ $noteClass }} cx-public-surface-plain cx-public-card-padding cx-public-card-hover hover:rotate-0 dark:bg-zinc-950 dark:shadow-black/20">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                        <flux:icon name="question-mark-circle" class="size-4" />
                                    </span>
                                    <h3 class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                </div>
                                <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $item->localized('content') ?: $item->localized('description') }}</p>
                            </article>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="cx-public-section-content cx-public-grid-compact md:grid-cols-2">
                    @foreach ($items as $item)
                        <article class="cx-public-surface-plain cx-public-card-padding cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                            <div class="flex items-start justify-between gap-4">
                                <h3 class="cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                <span class="inline-flex size-8 shrink-0 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon name="question-mark-circle" class="size-4" />
                                </span>
                            </div>
                            <p class="mt-4 cx-public-body text-zinc-600 dark:text-zinc-300">{{ $item->localized('content') ?: $item->localized('description') }}</p>
                        </article>
                    @endforeach
                </div>
            @endif
        <?php } ?>
