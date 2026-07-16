@php
    $type = (string) data_get($section, 'type');
    $isMuted = in_array($type, ['statistics', 'contact', 'partners', 'faq', 'how_to_order'], true);
    $sectionClass = $isMuted
        ? 'cx-public-section-bg-muted px-6 py-20'
        : 'cx-public-section-bg px-6 py-20';
@endphp

<div>
    <section class="{{ $sectionClass }} scroll-mt-24" aria-hidden="true">
        <div class="mx-auto cx-public-container">
            <div class="max-w-3xl animate-pulse">
                <div class="h-3 w-24 rounded-full bg-zinc-200/80 dark:bg-zinc-800"></div>
                <div class="mt-4 h-8 w-2/3 rounded-lg bg-zinc-200/80 dark:bg-zinc-800"></div>
                <div class="mt-4 grid gap-2">
                    <div class="h-3 w-full rounded-full bg-zinc-200/70 dark:bg-zinc-800/80"></div>
                    <div class="h-3 w-4/5 rounded-full bg-zinc-200/70 dark:bg-zinc-800/80"></div>
                </div>
            </div>

            <div class="mt-10 grid animate-pulse gap-4 md:grid-cols-3">
                @for ($i = 0; $i < 3; $i++)
                    <div class="min-h-36 rounded-lg bg-white/70 ring-1 ring-zinc-200/70 dark:bg-zinc-900/60 dark:ring-zinc-800"></div>
                @endfor
            </div>
        </div>
    </section>
</div>
