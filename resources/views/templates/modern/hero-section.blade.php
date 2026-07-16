@php
    $heroTitle = $this->value('title', $section?->localized('title'));
    $heroHasContent = filled($heroTitle)
        || filled($this->value('subtitle'))
        || filled($this->value('image'));
@endphp

<section class="bg-white px-6 py-16 dark:bg-zinc-950">
    @if (! $heroHasContent)
        <div class="mx-auto max-w-6xl">
            <x-public-section-empty-state
                :section="$section"
                icon="sparkles"
                :title="__('Uvodni sadržaj uskoro')"
                :description="__('Uvodni sadržaj prikazat će se ovdje kada bude spreman za objavu.')"
                compact
            />
        </div>
    @else
    <div class="mx-auto grid max-w-6xl gap-8 md:grid-cols-[minmax(0,1fr)_24rem] md:items-center">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-4xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $heroTitle }}</h1>
                @if ($this->value('subtitle'))
                    <p class="mt-4 text-lg leading-8 text-zinc-600 dark:text-zinc-300">{{ $this->value('subtitle') }}</p>
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
