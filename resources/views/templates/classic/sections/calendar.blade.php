@php
    $calendarLayout = (string) data_get($section, 'settings.layout_variant', 'calendar-split');
    $calendarLayout = in_array($calendarLayout, ['calendar-split', 'calendar-list', 'calendar-cards', 'calendar-carousel'], true) ? $calendarLayout : 'calendar-split';
    $calendarCarouselName = 'calendar-carousel-'.(string) data_get($section, 'uuid', data_get($section, 'id', 'section'));
    $calendarCarouselHasMore = $calendarLayout === 'calendar-carousel' && $this->hasMoreCarouselItems('calendar');
    $calendarEvents = $this->calendarEvents();
    $calendarEyebrow = $eyebrow;
    $calendarTitle = $title ?: __('Kalendar događaja');
    $calendarDescription = $description;
    $calendarEmptyTitle = __('Trenutno nema najavljenih događaja.');
    $calendarEmptyDescription = __('Novi termini pojavit će se ovdje kada budu potvrđeni.');
    $calendarEmptyIcon = 'calendar-days';
    $calendarSectionEditUser = auth()->user();
    $calendarSectionTeamId = data_get($section, 'team_id');
    $canShowCalendarSectionEditLink = $calendarSectionEditUser
        && filled(data_get($section, 'uuid'))
        && is_numeric($calendarSectionTeamId)
        && is_numeric(data_get($calendarSectionEditUser, 'current_team_id'))
        && (int) $calendarSectionTeamId === (int) data_get($calendarSectionEditUser, 'current_team_id')
        && \Illuminate\Support\Facades\Route::has('admin.sections.show');
    $canCycleCalendarSectionDesign = $canShowCalendarSectionEditLink
        && method_exists($this, 'canCycleSectionLayoutVariant')
        && $this->canCycleSectionLayoutVariant();
    $calendarSectionActionButtonBaseClass = 'inline-flex size-8 shrink-0 cursor-pointer items-center justify-center rounded-full transition duration-200 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary-200)] focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60 dark:focus:ring-[color:var(--niva-primary-700)]';
    $calendarSectionActionButtonToneClass = 'bg-white/80 text-zinc-500 ring-1 ring-zinc-950/10 shadow-sm shadow-zinc-950/5 hover:bg-[color:var(--niva-primary-50)] hover:text-[color:var(--niva-primary-800)] hover:ring-[color:var(--niva-primary-200)] dark:bg-zinc-950/70 dark:text-zinc-300 dark:ring-white/10 dark:hover:bg-zinc-900 dark:hover:text-[color:var(--niva-primary-200)]';
    $eventDescription = static function ($item): ?string {
        $source = $item->localized('description') ?: $item->localized('content');

        return filled($source) ? str((string) $source)->stripTags()->squish()->toString() : null;
    };
    $eventLocation = static fn ($item): ?string => filled(data_get($item, 'settings.location')) ? (string) data_get($item, 'settings.location') : null;
@endphp

@if ($calendarLayout === 'calendar-split')
    <div class="grid gap-10 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:items-start lg:gap-14">
        <div class="cx-public-copy-compact">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    @if ($calendarEyebrow)
                        <p class="{{ $eyebrowClass }}">{{ $calendarEyebrow }}</p>
                    @endif

                    @if ($calendarTitle)
                        <h2 class="{{ $titleClass }}">{{ $calendarTitle }}</h2>
                    @endif

                    @if ($calendarDescription)
                        <p class="{{ $descriptionClass }}">{{ $calendarDescription }}</p>
                    @endif

                    @if ($buttonText && $buttonUrl)
                        <a href="{{ $buttonUrl }}" class="mt-7 cx-public-button-primary">
                            {{ $buttonText }}
                        </a>
                    @endif
                </div>

                @if ($canShowCalendarSectionEditLink || $canCycleCalendarSectionDesign)
                    <div class="mt-1 flex shrink-0 items-center gap-2">
                        @if ($canShowCalendarSectionEditLink)
                            <x-public-section-edit-link :section="$section" :tone="$sectionTone" />
                        @endif

                        {{-- Privremeno skriveno radi testiranja sučelja bez izmjene izgleda pojedine sekcije.
                        @if ($canCycleCalendarSectionDesign)
                            <flux:tooltip :content="__('Prethodni izgled sekcije: :layout', ['layout' => $this->previousSectionLayoutVariantLabel()])" position="bottom">
                                <button
                                    type="button"
                                    wire:click="cycleSectionLayoutVariant('previous')"
                                    wire:loading.attr="disabled"
                                    wire:target="cycleSectionLayoutVariant"
                                    class="{{ $calendarSectionActionButtonBaseClass }} {{ $calendarSectionActionButtonToneClass }}"
                                    aria-label="{{ __('Prethodni izgled sekcije') }}"
                                    title="{{ __('Prethodni izgled sekcije') }}"
                                >
                                    <flux:icon name="chevron-left" class="size-4" wire:loading.remove wire:target="cycleSectionLayoutVariant" />
                                    <flux:icon.loading class="size-4" wire:loading wire:target="cycleSectionLayoutVariant" />
                                    <span class="sr-only">{{ __('Prethodni izgled sekcije') }}</span>
                                </button>
                            </flux:tooltip>

                            <flux:tooltip :content="__('Sljedeći izgled sekcije: :layout', ['layout' => $this->nextSectionLayoutVariantLabel()])" position="bottom">
                                <button
                                    type="button"
                                    wire:click="cycleSectionLayoutVariant('next')"
                                    wire:loading.attr="disabled"
                                    wire:target="cycleSectionLayoutVariant"
                                    class="{{ $calendarSectionActionButtonBaseClass }} {{ $calendarSectionActionButtonToneClass }}"
                                    aria-label="{{ __('Sljedeći izgled sekcije') }}"
                                    title="{{ __('Sljedeći izgled sekcije') }}"
                                >
                                    <flux:icon name="chevron-right" class="size-4" wire:loading.remove wire:target="cycleSectionLayoutVariant" />
                                    <flux:icon.loading class="size-4" wire:loading wire:target="cycleSectionLayoutVariant" />
                                    <span class="sr-only">{{ __('Sljedeći izgled sekcije') }}</span>
                                </button>
                            </flux:tooltip>
                        @endif
                        --}}
                    </div>
                @endif
            </div>
        </div>

        <div class="rounded-xl cx-public-panel-bg-muted p-4 cx-public-border sm:p-5">
            @if ($calendarEvents->isEmpty())
                <x-public-section-empty-state
                    :section="$section"
                    class="!border-0 !bg-transparent !p-0 !ring-0"
                    :icon="$calendarEmptyIcon"
                    :title="$calendarEmptyTitle"
                    :description="$calendarEmptyDescription"
                    align="start"
                    compact
                />
            @else
                <div class="divide-y cx-public-divide">
                    @foreach ($calendarEvents as $event)
                        @php
                            $eventDate = data_get($event, 'settings.event_date');
                            $timeRange = $this->calendarEventTimeRange(data_get($event, 'settings.starts_at'), data_get($event, 'settings.ends_at'));
                            $location = $eventLocation($event);
                            $descriptionText = $eventDescription($event);
                        @endphp

                        <article class="grid grid-cols-[4.25rem_minmax(0,1fr)] gap-4 py-5 first:pt-1 last:pb-1 sm:gap-5">
                            <div class="flex h-16 w-16 flex-col items-center justify-center rounded-xl bg-white text-center ring-1 ring-[color:var(--niva-primary-100)] dark:bg-zinc-950 dark:ring-zinc-800">
                                <span class="cx-public-date-month text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $this->calendarEventMonth($eventDate) }}</span>
                                <span class="mt-1 cx-public-date-day text-zinc-950 dark:text-white">{{ $this->calendarEventDay($eventDate) }}</span>
                            </div>

                            <div class="min-w-0">
                                <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $event->localized('title') }}</h3>

                                @if ($location)
                                    <p class="mt-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $location }}</p>
                                @elseif ($descriptionText)
                                    <p class="mt-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $descriptionText }}</p>
                                @endif

                                @if ($timeRange)
                                    <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $timeRange }}</p>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@elseif ($calendarLayout === 'calendar-list')
    @if ($calendarEvents->isEmpty())
        <x-public-section-empty-state
            :section="$section"
            class="cx-public-section-content"
            :icon="$calendarEmptyIcon"
            :title="$calendarEmptyTitle"
            :description="$calendarEmptyDescription"
            compact
        />
    @else
        <div class="cx-public-section-content">
            @foreach ($calendarEvents as $event)
                @php
                    $eventDate = data_get($event, 'settings.event_date');
                    $timeRange = $this->calendarEventTimeRange(data_get($event, 'settings.starts_at'), data_get($event, 'settings.ends_at'));
                    $location = $eventLocation($event);
                    $descriptionText = $eventDescription($event);
                @endphp

                <article class="cx-public-grid-tight pb-8 last:pb-0 sm:grid-cols-[8rem_minmax(0,1fr)] sm:gap-7">
                    <div class="sm:pt-1">
                        <p class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $this->calendarEventDateLabel($eventDate) }}</p>
                        @if ($timeRange)
                            <p class="mt-1 cx-public-meta text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $timeRange }}</p>
                        @endif
                    </div>

                    <div class="relative border-l cx-public-timeline-border pb-1 pl-6 sm:pl-7">
                        <span class="absolute -left-[5px] top-1.5 size-2.5 rounded-full cx-public-timeline-dot"></span>
                        <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $event->localized('title') }}</h3>

                        @if ($location)
                            <p class="mt-2 cx-public-item-text text-zinc-700 dark:text-zinc-200">{{ $location }}</p>
                        @endif

                        @if ($descriptionText)
                            <p class="mt-3 cx-public-copy cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $descriptionText }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@elseif ($calendarLayout === 'calendar-carousel')
    @if ($calendarEvents->isEmpty())
        <x-public-section-empty-state
            :section="$section"
            class="cx-public-section-content"
            :icon="$calendarEmptyIcon"
            :title="$calendarEmptyTitle"
            :description="$calendarEmptyDescription"
            compact
        />
    @else
        <div class="mt-6">
            @if ($calendarEvents->count() > 1 || $calendarCarouselHasMore)
                @include('niva-template::templates.classic.partials.carousel-controls', [
                    'name' => $calendarCarouselName,
                    'loadTarget' => 'calendar',
                    'hasMore' => $calendarCarouselHasMore,
                ])
            @endif

            <flux:carousel name="{{ $calendarCarouselName }}" class="-mx-4" :arrows="false" fade advance="page" track:class="px-4 scroll-px-4">
                @foreach ($calendarEvents as $event)
                    @php
                        $eventDate = data_get($event, 'settings.event_date');
                        $timeRange = $this->calendarEventTimeRange(data_get($event, 'settings.starts_at'), data_get($event, 'settings.ends_at'));
                        $location = $eventLocation($event);
                        $descriptionText = $eventDescription($event);
                    @endphp

                    <flux:carousel.slide class="w-4/5 sm:w-1/2 lg:w-1/3" wire:key="calendar-carousel-{{ data_get($event, 'id', $loop->index) }}">
                        <article class="flex h-full flex-col cx-public-surface cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20 dark:ring-zinc-800">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-[color:var(--niva-primary-50)] text-center text-[color:var(--niva-primary-800)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-200)] dark:ring-[color:var(--niva-primary-900)]">
                                    <span class="cx-public-date-month">{{ $this->calendarEventMonth($eventDate) }}</span>
                                    <span class="mt-1 cx-public-date-day">{{ $this->calendarEventDay($eventDate) }}</span>
                                </div>

                                @if ($timeRange)
                                    <p class="pt-1 text-right cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $timeRange }}</p>
                                @endif
                            </div>

                            <div class="mt-5 flex flex-1 flex-col">
                                <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $event->localized('title') }}</h3>

                                @if ($descriptionText)
                                    <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $descriptionText }}</p>
                                @endif

                                @if ($location)
                                    <p class="mt-5 cx-public-item-text text-zinc-700 dark:text-zinc-200">{{ $location }}</p>
                                @endif
                            </div>
                        </article>
                    </flux:carousel.slide>
                @endforeach
            </flux:carousel>
        </div>
    @endif
@else
    @if ($calendarEvents->isEmpty())
        <x-public-section-empty-state
            :section="$section"
            class="cx-public-section-content"
            :icon="$calendarEmptyIcon"
            :title="$calendarEmptyTitle"
            :description="$calendarEmptyDescription"
            compact
        />
    @else
        <div class="cx-public-section-content cx-public-grid sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($calendarEvents as $event)
                @php
                    $eventDate = data_get($event, 'settings.event_date');
                    $timeRange = $this->calendarEventTimeRange(data_get($event, 'settings.starts_at'), data_get($event, 'settings.ends_at'));
                    $location = $eventLocation($event);
                    $descriptionText = $eventDescription($event);
                @endphp

                <article class="flex h-full flex-col cx-public-surface cx-public-card-padding dark:bg-zinc-950 dark:shadow-black/20 dark:ring-zinc-800">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex h-16 w-16 shrink-0 flex-col items-center justify-center rounded-xl bg-[color:var(--niva-primary-50)] text-center text-[color:var(--niva-primary-800)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-200)] dark:ring-[color:var(--niva-primary-900)]">
                            <span class="cx-public-date-month">{{ $this->calendarEventMonth($eventDate) }}</span>
                            <span class="mt-1 cx-public-date-day">{{ $this->calendarEventDay($eventDate) }}</span>
                        </div>

                        @if ($timeRange)
                            <p class="pt-1 text-right cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $timeRange }}</p>
                        @endif
                    </div>

                    <div class="mt-5 flex flex-1 flex-col">
                        <h3 class="cx-public-item-title text-zinc-950 dark:text-white">{{ $event->localized('title') }}</h3>

                        @if ($descriptionText)
                            <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $descriptionText }}</p>
                        @endif

                        @if ($location)
                            <p class="mt-5 cx-public-item-text text-zinc-700 dark:text-zinc-200">{{ $location }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endif
