@php
    $heroButtonUrl = app(\IvanBaric\Corexis\Support\PublicUrl::class)->sanitize($this->value('button_url'));
    $heroTitle = $this->value('title', $section?->localized('title'));
    $heroHasContent = filled($this->value('badge'))
        || filled($heroTitle)
        || filled($this->value('subtitle'))
        || filled($this->value('image'))
        || (filled($this->value('button_text')) && filled($heroButtonUrl));
@endphp

<section class="border-b border-zinc-200 cx-public-section-bg-muted px-6 py-16 dark:border-zinc-800">
    @if (! $heroHasContent)
        <div class="cx-public-container">
            <x-public-section-empty-state
                :section="$section"
                icon="sparkles"
                :title="__('Uvodni sadržaj uskoro')"
                :description="__('Uvodni sadržaj prikazat će se ovdje kada bude spreman za objavu.')"
                compact
            />
        </div>
    @else
    <div class="cx-public-container grid gap-8 lg:grid-cols-[minmax(0,1fr)_24rem] lg:items-center">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                @if ($this->value('badge'))
                    <p class="text-sm font-medium uppercase tracking-wide text-zinc-500 dark:text-zinc-400">{{ $this->value('badge') }}</p>
                @endif
                <h1 class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $heroTitle }}</h1>
                @if ($this->value('subtitle'))
                    <p class="mt-4 cx-public-copy text-base leading-7 text-zinc-600 dark:text-zinc-300">{{ $this->value('subtitle') }}</p>
                @endif
                @if ($this->value('button_text') && $heroButtonUrl)
                    <a href="{{ $heroButtonUrl }}" class="mt-6 cx-public-button-primary">{{ $this->value('button_text') }}</a>
                @endif
            </div>

            <x-public-section-edit-link :section="$section" class="mt-1" />
        </div>
        @if ($this->value('image'))
            <img src="{{ $this->value('image') }}" alt="" class="aspect-[4/3] w-full rounded-lg object-cover" loading="lazy" decoding="async">
        @endif
    </div>
    @endif
</section>
