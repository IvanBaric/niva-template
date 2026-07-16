@php
    $carouselName = (string) ($name ?? '');
    $carouselLoadTarget = (string) ($loadTarget ?? '');
    $carouselHasMore = (bool) ($hasMore ?? false);
    $carouselButtonClass = 'rounded-full border border-zinc-200 size-8 flex justify-center items-center bg-white transition duration-200 hover:bg-zinc-50 dark:border-white/10 dark:bg-zinc-700 dark:hover:bg-zinc-600/75';
    $carouselControlClass = 'transition-opacity duration-200 [&[disabled]]:opacity-50 [&[disabled]]:pointer-events-none';
@endphp

<div
    x-data="{
        carouselLoading: false,
        carouselLoadingTimer: null,
        showCarouselLoading() {
            if (@js($carouselHasMore)) {
                this.carouselLoading = true
            }

            clearTimeout(this.carouselLoadingTimer)
            this.carouselLoadingTimer = setTimeout(() => this.carouselLoading = false, 450)
        },
    }"
    class="mb-4 flex items-center justify-end gap-3"
>
    <div x-cloak x-show="carouselLoading" x-transition.opacity.duration.150ms class="flex items-center gap-2 cx-public-meta text-zinc-500 dark:text-zinc-400">
        <flux:icon.loading class="size-4 text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]" />
        <span>{{ __('Učitavanje...') }}</span>
    </div>

    <div class="flex gap-2" data-flux-carousel-controls>
        <ui-carousel-button direction="previous" @if ($carouselName !== '') name="{{ $carouselName }}" @endif class="{{ $carouselControlClass }} data-at-start:opacity-50 data-at-start:pointer-events-none">
            <button type="button" aria-label="{{ __('Prethodni slajd') }}" class="{{ $carouselButtonClass }}">
                <flux:icon.chevron-left variant="mini" class="text-zinc-800 dark:text-zinc-200 rtl:-scale-x-100" />
            </button>
        </ui-carousel-button>

        <ui-carousel-button
            direction="next"
            @if ($carouselName !== '') name="{{ $carouselName }}" @endif
            @if ($carouselHasMore)
                x-on:click="showCarouselLoading()"
                wire:click="loadMoreCarousel('{{ $carouselLoadTarget }}')"
                wire:loading.attr="disabled"
                wire:target="loadMoreCarousel"
            @endif
            class="{{ $carouselControlClass }} data-at-end:opacity-50 data-at-end:pointer-events-none"
        >
            <button
                type="button"
                aria-label="{{ __('Sljedeći slajd') }}"
                class="{{ $carouselButtonClass }}"
                @if ($carouselHasMore)
                    wire:loading.attr="disabled"
                    wire:target="loadMoreCarousel"
                @endif
            >
                @if ($carouselHasMore)
                    <flux:icon.chevron-right variant="mini" class="text-zinc-800 dark:text-zinc-200 rtl:-scale-x-100" wire:loading.remove wire:target="loadMoreCarousel" />
                    <flux:icon.loading class="size-4 text-zinc-800 dark:text-zinc-200" wire:loading wire:target="loadMoreCarousel" />
                @else
                    <flux:icon.chevron-right variant="mini" class="text-zinc-800 dark:text-zinc-200 rtl:-scale-x-100" />
                @endif
            </button>
        </ui-carousel-button>
    </div>
</div>
