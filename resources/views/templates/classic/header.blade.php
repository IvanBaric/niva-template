@php
    $headerVariant = $this->headerVariant();
    $isHeroHeader = $headerVariant === 'header-1';
    $isEditorialHeader = $headerVariant === 'header-2';
    $isStickyHeader = $headerVariant === 'header-3';
    $isSplitHeader = $headerVariant === 'header-4';
    $isCraftHeader = $headerVariant === 'header-5';
    $isPrimarySplitHeader = $headerVariant === 'header-6';
    $isShowcaseHeader = $headerVariant === 'header-7';
    $isGalleryFrameHeader = $headerVariant === 'header-8';
    $usesImageBandHeader = $isShowcaseHeader || $isGalleryFrameHeader;
    $isLightImageHeader = $isSplitHeader || $isCraftHeader;
    $usesFloatingPillHeader = $isLightImageHeader || $isPrimarySplitHeader;
    $navItems = $this->navItems();
    $languageSwitcherComponent = $this->languageSwitcherComponent();
    $imageSources = $this->imageSources();
    $mobileImageSources = $this->mobileImageSources();
    $defaultImageSources = $this->defaultImageSources();
    $imageUrl = $imageSources['fallback'] ?? $this->imageUrl();
    $mobileImageUrl = $mobileImageSources['fallback'] ?? $this->mobileImageUrl();
    $headerImageSources = $imageSources !== [] ? $imageSources : ($mobileImageSources !== [] ? $mobileImageSources : $defaultImageSources);
    $mobileHeaderImageSources = $mobileImageSources !== [] ? $mobileImageSources : ($imageSources !== [] ? $imageSources : $defaultImageSources);
    $headerImageUrl = $headerImageSources['fallback'] ?? null;
    $mobileHeaderImageUrl = $mobileHeaderImageSources['fallback'] ?? null;
    $mobileImageMedia = '(max-width: 1023px)';
    $mobileImageObjectClass = '[object-position:center_68%] sm:[object-position:center_62%] lg:object-center';
    $mobileImageFilterClass = '[filter:saturate(1.08)_contrast(1.03)_brightness(1.02)]';
    $showLogo = $this->showLogo();
    $logoUrl = $showLogo ? $this->logoUrl() : null;
    $ctaItems = $this->ctaItems();
    $adminUrl = $this->adminUrl();
    $headerEditUrl = $this->headerEditUrl();
    $canAccessPublicManagement = $this->canAccessPublicManagement();
    $canCycleHeaderDesign = method_exists($this, 'canCycleHeaderVariant') && $this->canCycleHeaderVariant();
    $headerDesignButtonBaseClass = 'inline-flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-full transition duration-200 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary-200)] focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60';
    $headerDesignButtonToneClass = $isHeroHeader
        ? 'border border-white/20 bg-zinc-950/65 text-white shadow-sm shadow-zinc-950/20 backdrop-blur-md hover:bg-zinc-950/80 hover:text-white'
        : 'border border-zinc-950/10 bg-white/90 text-zinc-600 shadow-sm shadow-zinc-950/10 backdrop-blur-md hover:bg-[color:var(--niva-primary-50)] hover:text-[color:var(--niva-primary-800)]';
    $lightCtaClass = static fn (string $variant): string => $variant === 'primary'
        ? 'cx-public-button-primary'
        : 'cx-public-button-secondary';
    $darkCtaClass = static fn (string $variant): string => $variant === 'primary'
        ? 'cx-public-button-primary'
        : 'cx-public-button-inverse-secondary';
    $softCtaClass = static fn (string $variant): string => $variant === 'primary'
        ? 'cx-public-button-primary'
        : 'cx-public-button-secondary';
    $primarySplitCtaClass = static fn (string $variant): string => $variant === 'primary'
        ? 'cx-public-button-inverse'
        : 'cx-public-button-inverse-secondary';
    $largeHeight = match ($headerVariant) {
        'header-2' => 'min-h-[100svh] lg:min-h-[clamp(42rem,92svh,50rem)]',
        'header-3' => 'min-h-[clamp(40rem,92svh,56rem)]',
        'header-4' => 'min-h-[100svh] lg:min-h-[clamp(42rem,88svh,55rem)]',
        'header-5' => 'min-h-[100svh] lg:min-h-[clamp(43rem,92svh,58rem)]',
        'header-6' => 'min-h-[0] lg:min-h-[clamp(45rem,90svh,56rem)]',
        'header-7' => 'min-h-[clamp(44rem,88svh,58rem)]',
        'header-8' => 'min-h-[100svh] lg:min-h-[clamp(46rem,92svh,58rem)]',
        default => 'min-h-[36rem] sm:min-h-[40rem] lg:min-h-[clamp(42rem,78vh,48rem)]',
    };
    $smallHeight = $usesImageBandHeader
        ? 'min-h-[18rem] sm:min-h-[20rem] lg:min-h-[22rem]'
        : 'min-h-[12rem] sm:min-h-[15rem] lg:min-h-[16rem]';
@endphp

<header
    x-data="{ open: false, scrolled: false, parallaxY: 0, updateHeaderParallax() { if (! this.$refs.editorialParallax) { return } if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || window.innerWidth < 1024) { this.parallaxY = 0; return } const rect = this.$el.getBoundingClientRect(); const progress = Math.min(1, Math.max(0, -rect.top / Math.max(rect.height, 1))); this.parallaxY = Math.round(progress * 48); } }"
    x-init="scrolled = window.scrollY > 24; $nextTick(() => updateHeaderParallax())"
    x-on:scroll.window="scrolled = window.scrollY > 24; updateHeaderParallax()"
    x-on:resize.window="updateHeaderParallax()"
    x-on:keydown.escape.window="open = false"
    @class([
        'relative isolate',
        'overflow-hidden' => ! $isStickyHeader && ! ($isHeroHeader && $this->small),
        'overflow-visible' => $isStickyHeader || ($isHeroHeader && $this->small),
        'z-[90]' => $isStickyHeader,
        'z-[80]' => $isHeroHeader && $this->small,
        'font-sans',
        'niva-header-editorial' => $isEditorialHeader,
        'bg-zinc-950 text-white' => $isHeroHeader || $isStickyHeader,
        'bg-white text-zinc-950' => $isEditorialHeader,
        'bg-white text-zinc-950' => $isLightImageHeader,
        'bg-[color:var(--niva-primary-700)] text-white' => $isPrimarySplitHeader,
        'bg-white text-zinc-950' => $usesImageBandHeader,
        $largeHeight => ! $this->small,
        $smallHeight => $this->small,
    ])
>
    @if ($this->small && ! $usesImageBandHeader)
        @if ($headerImageUrl)
            <x-public.optimized-picture
                :sources="$headerImageSources"
                :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                :mobile-media="$mobileImageMedia"
                alt=""
                img-class="size-full object-cover {{ $mobileImageObjectClass }} [filter:saturate(1.08)_contrast(1.03)_brightness(1)]"
                class="absolute inset-0 -z-20 block size-full"
                loading="eager"
                fetchpriority="high"
            />
        @else
            <div class="absolute inset-0 -z-20 bg-[color:var(--niva-primary-100)]"></div>
        @endif
    @elseif (! $isEditorialHeader && ! $usesFloatingPillHeader && ! $usesImageBandHeader)
        @if ($headerImageUrl)
            <x-public.optimized-picture
                :sources="$headerImageSources"
                :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                :mobile-media="$mobileImageMedia"
                alt=""
                :img-class="\Illuminate\Support\Arr::toCssClasses([
                    'size-full object-cover',
                    $mobileImageObjectClass,
                    '[filter:saturate(1.18)_contrast(1.03)_brightness(1.1)]' => $isHeroHeader,
                    '[filter:saturate(1.12)_contrast(1.04)_brightness(1.04)]' => $isStickyHeader,
                ])"
                class="absolute inset-0 -z-20 block size-full"
                loading="eager"
                fetchpriority="high"
            />
        @else
            <div @class([
                'absolute inset-0 -z-20',
                'bg-[linear-gradient(135deg,#14532d_0%,#27272a_48%,#0f172a_100%)]' => $isHeroHeader,
                'bg-[linear-gradient(135deg,#dcebd2_0%,#4f7f62_42%,#1f2937_100%)]' => $isStickyHeader,
            ])></div>
        @endif
    @endif

    @if ($this->small && ! $usesImageBandHeader)
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(9,9,11,0.18)_0%,rgba(9,9,11,0.08)_46%,rgba(9,9,11,0.24)_100%)]"></div>
    @elseif ($isHeroHeader)
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(9,9,11,0.38)_0%,rgba(9,9,11,0.16)_44%,rgba(9,9,11,0.03)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-2/3 bg-[linear-gradient(0deg,rgba(9,9,11,0.34)_0%,rgba(9,9,11,0.08)_58%,rgba(9,9,11,0)_100%)]"></div>
    @elseif ($isEditorialHeader)
        <div class="absolute inset-0 -z-30 bg-zinc-950"></div>
        @if ($headerImageUrl)
            <x-public.optimized-picture
                :sources="$headerImageSources"
                :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                :mobile-media="$mobileImageMedia"
                alt=""
                img-class="size-full object-cover object-center [filter:saturate(1.08)_contrast(1.03)_brightness(1.04)]"
                x-ref="editorialParallax"
                x-bind:style="'transform: translate3d(0, ' + parallaxY + 'px, 0)'"
                class="absolute inset-x-0 -top-12 -bottom-12 -z-20 block will-change-transform motion-reduce:transform-none"
                loading="eager"
                fetchpriority="high"
            />
        @endif
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(9,9,11,0.34)_0%,rgba(9,9,11,0.12)_32%,rgba(9,9,11,0.16)_70%,rgba(9,9,11,0.34)_100%)]"></div>
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_center,rgba(9,9,11,0.44)_0%,rgba(9,9,11,0.28)_38%,rgba(9,9,11,0.16)_68%,rgba(9,9,11,0.30)_100%)]"></div>
        <div class="absolute inset-x-0 bottom-0 -z-10 h-1/3 bg-[linear-gradient(0deg,rgba(9,9,11,0.24)_0%,rgba(9,9,11,0)_100%)]"></div>
    @elseif ($isSplitHeader)
        <div class="pointer-events-none absolute inset-0 z-0 bg-[#fffaf1]"></div>
        <div @class([
            'pointer-events-none absolute inset-y-0 -right-24 z-0 hidden origin-top-left skew-x-[12deg] overflow-hidden lg:block',
            'w-[calc(61%+6rem)] rounded-bl-[18rem] lg:rounded-bl-[22rem]' => ! $this->small,
            'w-[calc(63%+5.5rem)] rounded-bl-[8rem] lg:rounded-bl-[10rem]' => $this->small,
        ])>
            <div class="absolute inset-y-0 -left-20 -right-20 -skew-x-[12deg]">
                @if ($headerImageUrl)
                    <x-public.optimized-picture
                        :sources="$headerImageSources"
                        alt=""
                        img-class="size-full object-cover [filter:saturate(1.08)_contrast(1.03)_brightness(1.02)]"
                        class="absolute inset-0 block size-full"
                        loading="eager"
                        fetchpriority="high"
                    />
                @else
                    <div class="absolute inset-0 bg-[color:var(--niva-primary-100)]"></div>
                @endif
            </div>
        </div>
        <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden lg:hidden">
            @if ($mobileHeaderImageUrl)
                <x-public.optimized-picture
                    :sources="$mobileHeaderImageSources"
                    alt=""
                    img-class="size-full object-cover {{ $mobileImageObjectClass }} {{ $mobileImageFilterClass }}"
                    class="absolute inset-0 block size-full"
                    loading="eager"
                    fetchpriority="high"
                />
            @else
                <div class="absolute inset-0 bg-[color:var(--niva-primary-100)]"></div>
            @endif
        </div>
        <div class="pointer-events-none absolute inset-0 z-[1] bg-[linear-gradient(180deg,rgba(255,255,255,0.32)_0%,rgba(255,255,255,0.12)_44%,rgba(255,255,255,0)_100%)] lg:hidden"></div>
    @elseif ($isCraftHeader)
        <div class="absolute inset-0 -z-20 bg-[#fffaf1]"></div>
        <div class="absolute inset-0 -z-20 overflow-hidden lg:hidden">
            @if ($mobileHeaderImageUrl)
                <x-public.optimized-picture
                    :sources="$mobileHeaderImageSources"
                    alt=""
                    img-class="size-full object-cover {{ $mobileImageObjectClass }} {{ $mobileImageFilterClass }}"
                    class="absolute inset-0 block size-full"
                    loading="eager"
                    fetchpriority="high"
                />
            @else
                <div class="absolute inset-0 bg-[color:var(--niva-primary-100)]"></div>
            @endif
        </div>
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(180deg,rgba(255,250,241,0.30)_0%,rgba(255,250,241,0.10)_44%,rgba(255,250,241,0)_100%)] lg:hidden"></div>
        <div class="absolute inset-y-0 right-0 -z-20 hidden w-[64%] overflow-hidden bg-[color:var(--niva-primary-100)] lg:block">
            @if ($headerImageUrl)
                <x-public.optimized-picture
                    :sources="$headerImageSources"
                    alt=""
                    img-class="size-full object-cover [filter:saturate(1.08)_contrast(1.03)_brightness(1.02)]"
                    class="absolute inset-0 block size-full"
                    loading="eager"
                    fetchpriority="high"
                />
            @else
                <div class="absolute inset-0 bg-[color:var(--niva-primary-100)]"></div>
            @endif
            <div class="pointer-events-none absolute inset-y-0 left-0 w-[42%]" style="background: linear-gradient(90deg, #fffaf1 0%, rgba(255,250,241,0.82) 30%, rgba(255,250,241,0.28) 68%, transparent 100%);"></div>
        </div>
    @elseif ($isPrimarySplitHeader)
        <div class="absolute inset-0 -z-30 hidden bg-[color:var(--niva-primary-700)] lg:block"></div>
        <div class="absolute inset-y-0 right-0 -z-20 hidden w-[68%] overflow-hidden bg-[color:var(--niva-primary-100)] lg:block">
            @if ($headerImageUrl)
                <x-public.optimized-picture
                    :sources="$headerImageSources"
                    alt=""
                    img-class="size-full object-cover object-center"
                    class="absolute inset-0 block size-full"
                    loading="eager"
                    fetchpriority="high"
                />
            @else
                <div class="absolute inset-0 bg-[color:var(--niva-primary-100)]"></div>
            @endif
            <div class="pointer-events-none absolute inset-0 bg-[color:var(--niva-primary)] opacity-5"></div>
            <div class="pointer-events-none absolute inset-y-0 left-0 w-[34%]" style="background: linear-gradient(90deg, var(--niva-primary-700) 0%, color-mix(in srgb, var(--niva-primary-700) 72%, transparent) 48%, transparent 100%);"></div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-[linear-gradient(0deg,rgba(9,9,11,0.18)_0%,rgba(9,9,11,0)_100%)]"></div>
        </div>
        <div class="absolute inset-y-0 left-0 -z-10 hidden w-[48%] lg:block" style="background: linear-gradient(90deg, var(--niva-primary-700) 0%, var(--niva-primary-700) 70%, color-mix(in srgb, var(--niva-primary-700) 82%, transparent) 88%, transparent 100%);"></div>
        <div class="absolute inset-0 -z-20 bg-[color:var(--niva-primary-700)] lg:hidden"></div>
    @elseif ($usesImageBandHeader)
        <div class="absolute inset-0 -z-20 bg-white"></div>
    @elseif ($isStickyHeader)
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(9,9,11,0.08)_0%,rgba(9,9,11,0.01)_42%,rgba(9,9,11,0.28)_100%)]"></div>
        <div class="absolute inset-x-0 -bottom-px -z-10 h-36 bg-[linear-gradient(0deg,rgba(255,255,255,0.82)_0%,rgba(255,255,255,0.64)_18%,rgba(255,255,255,0.26)_48%,rgba(255,255,255,0)_100%)]"></div>
    @else
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,rgba(9,9,11,0.12)_0%,rgba(9,9,11,0.02)_42%,rgba(9,9,11,0.42)_100%)]"></div>
        <div class="absolute inset-x-0 -bottom-px -z-10 h-36 bg-[linear-gradient(0deg,rgb(255,255,255)_0%,rgba(255,255,255,0.86)_14%,rgba(255,255,255,0.42)_46%,rgba(255,255,255,0)_100%)]"></div>
    @endif

    <div @class([
        'absolute right-4 top-4 z-[120] items-center gap-2 sm:right-5',
        'hidden lg:flex',
    ])>
        @if ($headerEditUrl)
            <flux:tooltip :content="__('Uredi zaglavlje')" position="bottom">
                <button
                    type="button"
                    wire:click="$dispatch('pages-open-public-template-part-editor', { part: 'header' })"
                    data-public-template-edit-link="template_header"
                    class="{{ $headerDesignButtonBaseClass }} {{ $headerDesignButtonToneClass }}"
                    aria-label="{{ __('Uredi zaglavlje') }}"
                    title="{{ __('Uredi zaglavlje') }}"
                >
                    <flux:icon name="pencil" class="size-4" />
                    <span class="sr-only">{{ __('Uredi zaglavlje') }}</span>
                </button>
            </flux:tooltip>
        @endif

        {{-- Privremeno skriveno radi testiranja sučelja bez izmjene izgleda zaglavlja.
        @if ($canCycleHeaderDesign)
            <flux:tooltip :content="__('Prethodni izgled zaglavlja: :layout', ['layout' => $this->previousHeaderVariantLabel()])" position="bottom">
                <button
                    type="button"
                    wire:click="cycleHeaderVariant('previous')"
                    wire:loading.attr="disabled"
                    wire:target="cycleHeaderVariant"
                    class="{{ $headerDesignButtonBaseClass }} {{ $headerDesignButtonToneClass }}"
                    aria-label="{{ __('Prethodni izgled zaglavlja') }}"
                    title="{{ __('Prethodni izgled zaglavlja') }}"
                >
                    <flux:icon name="chevron-left" class="size-4" wire:loading.remove wire:target="cycleHeaderVariant" />
                    <flux:icon.loading class="size-4" wire:loading wire:target="cycleHeaderVariant" />
                    <span class="sr-only">{{ __('Prethodni izgled zaglavlja') }}</span>
                </button>
            </flux:tooltip>

            <flux:tooltip :content="__('Sljedeći izgled zaglavlja: :layout', ['layout' => $this->nextHeaderVariantLabel()])" position="bottom">
                <button
                    type="button"
                    wire:click="cycleHeaderVariant('next')"
                    wire:loading.attr="disabled"
                    wire:target="cycleHeaderVariant"
                    class="{{ $headerDesignButtonBaseClass }} {{ $headerDesignButtonToneClass }}"
                    aria-label="{{ __('Sljedeći izgled zaglavlja') }}"
                    title="{{ __('Sljedeći izgled zaglavlja') }}"
                >
                    <flux:icon name="chevron-right" class="size-4" wire:loading.remove wire:target="cycleHeaderVariant" />
                    <flux:icon.loading class="size-4" wire:loading wire:target="cycleHeaderVariant" />
                    <span class="sr-only">{{ __('Sljedeći izgled zaglavlja') }}</span>
                </button>
            </flux:tooltip>
        @endif
        --}}

        @if ($canAccessPublicManagement)
            <x-public-user-menu :admin-url="$adminUrl" />
        @elseif (auth()->guest())
            <x-public-login-trigger />
        @endif
    </div>

    <div @class([
        'mx-auto flex w-full flex-col',
        'px-6' => ! $isShowcaseHeader,
        'px-0' => $isShowcaseHeader,
        'max-w-[92rem]' => $usesFloatingPillHeader || $isGalleryFrameHeader,
        'max-w-none' => $isShowcaseHeader,
        'max-w-[82rem]' => $isEditorialHeader,
        'cx-public-container' => ! $isEditorialHeader && ! $usesFloatingPillHeader && ! $usesImageBandHeader,
        'py-5' => ! $isStickyHeader && ! $usesFloatingPillHeader && ! $usesImageBandHeader,
        'py-6' => $isStickyHeader,
        'py-6 lg:py-7' => $usesFloatingPillHeader && ! ($isSplitHeader && $this->small),
        'py-4 lg:py-5' => $isSplitHeader && $this->small,
        'py-0' => $usesImageBandHeader,
        $largeHeight => ! $this->small,
        $smallHeight => $this->small,
    ])>
        <div
            @class([
                'flex items-center justify-between gap-3',
                'relative z-20' => ! $isStickyHeader,
                'h-16 rounded-full bg-white/95 px-5 py-2.5 shadow-sm shadow-zinc-950/10 backdrop-blur-md' => $isEditorialHeader,
                'rounded-full bg-white/95 px-4 py-1.5 shadow-lg shadow-zinc-950/10 ring-1 ring-zinc-200/70 backdrop-blur-md' => $usesFloatingPillHeader && ! $isSplitHeader,
                'mx-auto w-full max-w-[72rem] rounded-full bg-white/95 px-3 py-2 shadow-lg shadow-zinc-950/10 ring-1 ring-zinc-200/70 backdrop-blur-md' => $isSplitHeader,
                'h-20 border-b border-zinc-200/80 bg-white px-6 shadow-sm shadow-zinc-950/5 lg:h-24 lg:px-14' => $isShowcaseHeader,
                'h-20 border-b border-zinc-200/80 bg-white lg:h-[5.5rem]' => $isGalleryFrameHeader,
                'fixed left-1/2 top-3 z-[100] w-[min(calc(100vw-2rem),72rem)] -translate-x-1/2 rounded-full border px-3 py-2 shadow-lg backdrop-blur-xl transition duration-300' => $isStickyHeader,
            ])
            @if ($isStickyHeader)
                x-bind:class="scrolled ? 'border-white/80 bg-white/95 shadow-zinc-950/20' : 'border-white/60 bg-white/80 shadow-zinc-950/10'"
            @endif
        >
            <a href="{{ $this->homeUrl() }}" aria-label="{{ $this->organizationName() }}" @class([
                'inline-flex shrink-0 cursor-pointer items-center gap-2 text-base font-semibold transition',
                'rounded-full bg-zinc-950/70 px-3 py-2 text-white shadow-sm ring-1 ring-white/20 backdrop-blur-md hover:bg-zinc-950/80' => $isHeroHeader,
                'min-w-0 px-1 py-1 text-[15px] font-bold text-zinc-950' => $isEditorialHeader,
                'rounded-full px-3 py-1.5 text-zinc-950 hover:bg-zinc-950/5' => $isStickyHeader,
                'min-w-0 gap-3 py-1 pr-2 text-[17px] font-bold text-zinc-950 hover:text-[color:var(--niva-primary-800)]' => $usesFloatingPillHeader && ! $isSplitHeader,
                'min-w-0 gap-2.5 py-0 pr-1.5 text-[18px] font-bold text-zinc-950 hover:text-[color:var(--niva-primary-800)]' => $isSplitHeader,
                'min-w-0 gap-3 py-1 text-[18px] font-bold text-zinc-950 hover:text-[color:var(--niva-primary-800)]' => $isShowcaseHeader,
                'min-w-0 gap-3.5 py-1 text-[18px] font-bold text-zinc-950 hover:text-[color:var(--niva-primary-800)]' => $isGalleryFrameHeader,
            ])>
                @if ($showLogo && $logoUrl)
                    <span @class([
                        'flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-white transition duration-200',
                        'size-9 max-h-9 max-w-9' => ! $isGalleryFrameHeader,
                        'size-10 max-h-10 max-w-10' => $isGalleryFrameHeader,
                        'ring-1 ring-white/30' => $isHeroHeader,
                        'ring-1 ring-zinc-200/70' => ! $isHeroHeader,
                        'shadow-sm' => ! $isStickyHeader,
                        'cx-public-border' => $isShowcaseHeader,
                    ])>
                        <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="size-full max-h-full max-w-full object-contain" loading="eager" decoding="async">
                    </span>
                @endif
                <span class="truncate">{{ ($isHeroHeader || $isEditorialHeader || $isStickyHeader || $isSplitHeader || $isCraftHeader || $isPrimarySplitHeader || $usesImageBandHeader) ? $this->title() : __('Školska zadruga') }}</span>
            </a>

            <nav @class([
                'hidden max-w-full flex-wrap items-center text-sm',
                'lg:flex',
                'w-fit gap-x-3 gap-y-1 rounded-full border border-white/20 bg-zinc-950/70 px-2.5 py-1.5 text-white shadow-sm shadow-zinc-950/20 backdrop-blur-md' => $isHeroHeader,
                'w-fit gap-x-8 gap-y-2 text-[14px] text-zinc-700' => $isEditorialHeader,
                'w-fit gap-x-9 gap-y-2 text-[14px] text-zinc-700' => $usesFloatingPillHeader && ! $isSplitHeader,
                'w-fit gap-x-6 gap-y-1 text-[15px] text-zinc-700' => $isSplitHeader,
                'mx-auto w-fit gap-x-4 gap-y-2 text-[14px] text-zinc-700' => $isShowcaseHeader,
                'mx-auto w-fit gap-x-7 gap-y-2 text-[15px] text-zinc-700' => $isGalleryFrameHeader,
                'min-w-0 flex-1 justify-end gap-x-3 gap-y-1 text-zinc-700' => $isStickyHeader,
            ]) data-section-nav>
                @foreach ($navItems as $item)
                    <div class="group relative flex items-center">
                    <a href="{{ $item['href'] }}" @if (($item['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif data-section-nav-link data-active-class="" data-inactive-class="" @class([
                        'cursor-pointer font-medium transition',
                        'rounded-full px-3.5 py-2' => $isHeroHeader,
                        'rounded-full px-3 py-1.5' => ! $isHeroHeader && ! $isEditorialHeader && ! $usesFloatingPillHeader && ! $usesImageBandHeader,
                        'border-b-2 px-0 py-1.5' => ($isEditorialHeader || $usesFloatingPillHeader) && ! $isSplitHeader,
                        'relative rounded-md px-3 py-2' => $isShowcaseHeader,
                        'relative px-1 py-2.5 after:absolute after:inset-x-0 after:-bottom-2.5 after:h-0.5 after:origin-center after:rounded-full after:bg-[color:var(--niva-primary-600)] after:transition after:duration-200' => $isGalleryFrameHeader,
                        'border-b-2 px-0 py-1' => $isSplitHeader,
                        'bg-white text-zinc-950 shadow-sm' => $item['active'] && $isHeroHeader,
                        'text-zinc-100 hover:bg-white/10 hover:text-white' => ! $item['active'] && $isHeroHeader,
                        'border-[color:var(--niva-primary-700)] text-[color:var(--niva-primary-800)]' => $item['active'] && $isEditorialHeader,
                        'border-transparent text-zinc-700 hover:border-[color:var(--niva-primary-200)] hover:text-[color:var(--niva-primary-800)]' => ! $item['active'] && $isEditorialHeader,
                        'bg-zinc-950 text-white shadow-sm' => $item['active'] && $isStickyHeader,
                        'text-zinc-700 hover:bg-zinc-950/5 hover:text-[color:var(--niva-primary-800)]' => ! $item['active'] && $isStickyHeader,
                        'border-[color:var(--niva-primary-700)] text-[color:var(--niva-primary-800)]' => $item['active'] && $usesFloatingPillHeader,
                        'border-transparent text-zinc-700 hover:text-[color:var(--niva-primary-800)]' => ! $item['active'] && $usesFloatingPillHeader,
                        'font-semibold text-[color:var(--niva-primary-800)] after:absolute after:inset-x-3 after:-bottom-1 after:h-0.5 after:rounded-full after:bg-[color:var(--niva-primary-600)]' => $item['active'] && $isShowcaseHeader,
                        'text-zinc-700 hover:bg-zinc-100/70 hover:text-[color:var(--niva-primary-800)]' => ! $item['active'] && $isShowcaseHeader,
                        'font-semibold text-[color:var(--niva-primary-800)] after:scale-x-100' => $item['active'] && $isGalleryFrameHeader,
                        'text-zinc-600 after:scale-x-0 hover:text-[color:var(--niva-primary-800)] hover:after:scale-x-100' => ! $item['active'] && $isGalleryFrameHeader,
                    ])>
                        {{ $item['label'] }}
                    </a>
                    @if (($item['children'] ?? []) !== [])
                        <button
                            type="button"
                            aria-haspopup="true"
                            aria-label="{{ __('Otvori podstranice: :page', ['page' => $item['label']]) }}"
                            class="-ml-1 inline-flex size-7 cursor-pointer items-center justify-center rounded-full text-current opacity-70 transition hover:bg-black/5 hover:opacity-100 focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary-200)]"
                        >
                            <flux:icon name="chevron-down" class="size-3.5 transition duration-200 group-hover:rotate-180 group-focus-within:rotate-180" />
                        </button>

                        <div class="invisible absolute left-0 top-full z-[150] w-64 translate-y-1 pt-3 opacity-0 transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                            <div class="rounded-lg border border-zinc-200 bg-white p-1.5 text-zinc-950 shadow-xl shadow-zinc-950/15 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                @foreach ($item['children'] as $child)
                                    <div
                                        x-data="{ submenuOpen: false, submenuSide: 'right' }"
                                        x-on:mouseenter="submenuSide = window.innerWidth - $el.getBoundingClientRect().right >= 264 ? 'right' : 'left'; submenuOpen = true"
                                        x-on:mouseleave="submenuOpen = false"
                                        x-on:focusin="submenuSide = window.innerWidth - $el.getBoundingClientRect().right >= 264 ? 'right' : 'left'; submenuOpen = true"
                                        x-on:focusout="if (! $el.contains($event.relatedTarget)) submenuOpen = false"
                                        class="relative"
                                        data-navigation-submenu-item
                                    >
                                        <a href="{{ $child['href'] }}" @if (($child['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif @class([
                                            'flex cursor-pointer items-center justify-between gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition',
                                            'bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-800)] dark:bg-white/10 dark:text-white' => $child['active'],
                                            'text-zinc-700 hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)] dark:text-zinc-200 dark:hover:bg-white/10 dark:hover:text-white' => ! $child['active'],
                                        ])>
                                            <span class="min-w-0 truncate">{{ $child['label'] }}</span>
                                            @if (($child['children'] ?? []) !== [])
                                                <flux:icon name="chevron-right" class="size-3.5 shrink-0 opacity-45" />
                                            @endif
                                        </a>

                                        @if (($child['children'] ?? []) !== [])
                                            <div
                                                x-cloak
                                                x-show="submenuOpen"
                                                x-transition:enter="transition ease-out duration-150"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-in duration-100"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                x-bind:style="submenuSide === 'right' ? 'left: 100%; padding-left: 0.5rem;' : 'right: 100%; padding-right: 0.5rem;'"
                                                class="absolute -top-[7px] z-[160] w-64"
                                                data-navigation-submenu
                                            >
                                                <div class="rounded-lg border border-zinc-200 bg-white p-1.5 text-zinc-950 shadow-xl shadow-zinc-950/15 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white">
                                                    @foreach ($child['children'] as $grandchild)
                                                        <a href="{{ $grandchild['href'] }}" @if (($grandchild['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif @class([
                                                            'flex cursor-pointer items-center rounded-md px-3 py-2.5 text-sm font-medium transition',
                                                            'bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-800)] dark:bg-white/10 dark:text-white' => $grandchild['active'],
                                                            'text-zinc-700 hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)] dark:text-zinc-200 dark:hover:bg-white/10 dark:hover:text-white' => ! $grandchild['active'],
                                                        ])>
                                                            <span class="min-w-0 truncate">{{ $grandchild['label'] }}</span>
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    </div>
                @endforeach
            </nav>

            @if ($languageSwitcherComponent)
                <div class="hidden w-28 shrink-0 lg:block" data-public-language-switcher>
                    @livewire($languageSwitcherComponent, [
                        'label' => null,
                        'showFlags' => (bool) config('niva-template.language_switcher.show_flags', true),
                    ], key('niva-template-language-desktop-'.data_get($organization, 'slug')))
                </div>
            @endif

            <button
                type="button"
                x-on:click="open = ! open"
                x-bind:aria-expanded="open.toString()"
                aria-label="{{ __('Otvori izbornik') }}"
                @class([
                    'inline-flex size-10 cursor-pointer items-center justify-center rounded-full shadow-sm shadow-zinc-950/10 backdrop-blur-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/90',
                    'lg:hidden',
                    'border border-white/20 bg-zinc-950/75 text-white hover:bg-zinc-950/90' => $isHeroHeader,
                    'bg-white/90 text-zinc-950 hover:bg-white' => $isEditorialHeader,
                    'bg-white/90 text-zinc-950 hover:bg-white' => $usesFloatingPillHeader,
                    'border border-zinc-950/10 bg-white text-zinc-950 hover:bg-zinc-50' => $usesImageBandHeader,
                    'border border-zinc-950/10 bg-white/90 text-zinc-950 hover:bg-white' => $isStickyHeader,
                ])
            >
                <span class="relative block h-4 w-5">
                    <span class="absolute left-0 top-0 h-0.5 w-5 rounded-full bg-current transition duration-300" x-bind:class="open ? 'translate-y-[7px] rotate-45' : ''"></span>
                    <span class="absolute left-0 top-[7px] h-0.5 w-5 rounded-full bg-current transition duration-200" x-bind:class="open ? 'opacity-0' : 'opacity-100'"></span>
                    <span class="absolute left-0 top-[14px] h-0.5 w-5 rounded-full bg-current transition duration-300" x-bind:class="open ? '-translate-y-[7px] -rotate-45' : ''"></span>
                </span>
            </button>

            <template x-teleport="body">
                <div>
                    <div
                        x-cloak
                        x-show="open"
                        x-transition.opacity.duration.200ms
                        x-on:click="open = false"
                        @class([
                            'fixed inset-0 z-[900] bg-zinc-950/30 backdrop-blur-[2px]',
                            'lg:hidden',
                        ])
                    ></div>

                    <nav
                        x-cloak
                        x-show="open"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-3 scale-[0.98]"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-180"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
                        x-on:click.outside="open = false"
                        @class([
                        'fixed right-4 top-24 z-[910] w-[min(21rem,calc(100vw-2rem))] overflow-hidden rounded-2xl border border-white/80 bg-white/95 p-2 text-zinc-950 shadow-2xl shadow-zinc-950/20 backdrop-blur-xl sm:right-6',
                        'lg:hidden',
                    ])
                        data-section-nav
                    >
                        <div class="px-3 pb-2 pt-2 text-xs font-semibold uppercase tracking-wide text-zinc-500">{{ __('Izbornik') }}</div>
                        <div class="grid gap-1">
                            @foreach ($navItems as $item)
                                @if (($item['children'] ?? []) !== [])
                                    <details class="group/mobile overflow-hidden rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950" @if ($item['active']) open @endif>
                                        <summary @class([
                                            'flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-3 text-[15px] font-semibold leading-5 transition marker:hidden',
                                            'text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-200)]' => $item['active'],
                                            'text-zinc-800 hover:bg-zinc-100 dark:text-zinc-100 dark:hover:bg-zinc-900' => ! $item['active'],
                                        ])>
                                            <span>{{ $item['label'] }}</span>
                                            <flux:icon name="chevron-down" class="size-4 shrink-0 text-zinc-400 transition group-open/mobile:rotate-180" />
                                        </summary>
                                        <div class="space-y-1 border-t border-zinc-200 p-1.5 dark:border-zinc-800">
                                            <a href="{{ $item['href'] }}" @if (($item['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif x-on:click="open = false" class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)] dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:text-white">
                                                <flux:icon name="arrow-right" class="size-3.5" />
                                                <span>{{ __('Pregled: :page', ['page' => $item['label']]) }}</span>
                                            </a>
                                            @foreach ($item['children'] as $child)
                                                <div>
                                                    <a href="{{ $child['href'] }}" @if (($child['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif x-on:click="open = false" @class([
                                                        'flex cursor-pointer items-center justify-between gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
                                                        'bg-zinc-950 text-white' => $child['active'],
                                                        'text-zinc-700 hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)] dark:text-zinc-200 dark:hover:bg-zinc-900 dark:hover:text-white' => ! $child['active'],
                                                    ])>
                                                        <span>{{ $child['label'] }}</span>
                                                        @if (($child['children'] ?? []) !== [])
                                                            <flux:icon name="chevron-down" class="size-3.5 opacity-45" />
                                                        @endif
                                                    </a>

                                                    @if (($child['children'] ?? []) !== [])
                                                        <div class="ml-4 border-l border-zinc-200 pl-2 dark:border-zinc-700">
                                                            @foreach ($child['children'] as $grandchild)
                                                                <a href="{{ $grandchild['href'] }}" @if (($grandchild['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif x-on:click="open = false" @class([
                                                                    'flex cursor-pointer items-center rounded-lg px-3 py-2 text-[13px] font-medium transition',
                                                                    'bg-zinc-950 text-white' => $grandchild['active'],
                                                                    'text-zinc-600 hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)] dark:text-zinc-300 dark:hover:bg-zinc-900 dark:hover:text-white' => ! $grandchild['active'],
                                                                ])>
                                                                    <span>{{ $grandchild['label'] }}</span>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </details>
                                @else
                                    <a href="{{ $item['href'] }}" @if (($item['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif data-section-nav-link data-active-class="" data-inactive-class="" x-on:click="open = false" @class([
                                        'flex cursor-pointer items-center justify-between rounded-xl px-3 py-3 text-[15px] font-semibold leading-5 transition duration-200',
                                        'bg-zinc-950 text-white shadow-sm' => $item['active'],
                                        'text-zinc-800 hover:bg-zinc-100 hover:text-[color:var(--niva-primary-800)]' => ! $item['active'],
                                    ])>
                                        <span>{{ $item['label'] }}</span>
                                        <span class="text-lg leading-none opacity-45">&rsaquo;</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                        @if ($languageSwitcherComponent)
                            <div class="mt-2 border-t border-zinc-200 px-3 pb-1 pt-3 dark:border-zinc-800" data-public-mobile-language-switcher>
                                @livewire($languageSwitcherComponent, [
                                    'label' => __('Jezik'),
                                    'showFlags' => (bool) config('niva-template.language_switcher.show_flags', true),
                                ], key('niva-template-language-mobile-'.data_get($organization, 'slug')))
                            </div>
                        @endif
                        @if ($canAccessPublicManagement)
                            <x-public-user-menu :admin-url="$adminUrl" mobile />
                        @elseif (auth()->guest())
                            <x-public-login-trigger mobile />
                        @endif
                    </nav>
                </div>
            </template>
        </div>

        @if ($this->small && $usesImageBandHeader)
            <div @class([
                'relative flex flex-1 overflow-hidden border-b border-zinc-200/70 bg-[color:var(--niva-primary-100)]',
                'niva-showcase-small-media' => $isShowcaseHeader,
                'niva-gallery-frame-small-media mx-0 mb-5 rounded-xl' => $isGalleryFrameHeader,
            ])>
                @if ($headerImageUrl)
                    <x-public.optimized-picture
                        :sources="$headerImageSources"
                        :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                        :mobile-media="$mobileImageMedia"
                        alt=""
                        img-class="size-full object-cover {{ $mobileImageObjectClass }} lg:[object-position:center_56%] [filter:saturate(1.04)_contrast(1.03)_brightness(1.02)]"
                        class="absolute inset-0 block size-full"
                        loading="eager"
                        fetchpriority="high"
                    />
                @else
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--niva-primary-100) 0%, var(--niva-primary-200) 54%, var(--niva-primary-50) 100%);"></div>
                @endif

                <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(180deg,rgba(255,255,255,0.08)_0%,rgba(255,255,255,0)_40%,rgba(255,255,255,0.06)_100%)]"></div>
                <div class="pointer-events-none absolute inset-x-0 bottom-0 h-20 bg-[linear-gradient(0deg,rgba(255,255,255,0.68)_0%,rgba(255,255,255,0)_100%)]"></div>
            </div>
        @endif

        @if (! $this->small)
        @if ($isHeroHeader)
            <div @class([
                'flex-1 items-end pb-10 sm:pb-9 lg:pb-8',
                'flex' => ! $this->small,
                'hidden lg:flex' => $this->small,
                'pt-16 sm:pt-20' => ! $this->small,
                'pt-14' => $this->small,
            ])>
                <div class="cx-public-copy w-full rounded-3xl border border-white/20 bg-zinc-950/[0.64] p-5 text-white shadow-2xl shadow-zinc-950/20 backdrop-blur-[2px] sm:rounded-[1.75rem] sm:p-8">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-6">
                        @if ($logoUrl)
                            <div class="flex size-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white p-0 shadow-sm ring-1 ring-white/30 sm:h-[6.75rem] sm:w-[6.75rem] sm:rounded-2xl lg:h-[7.5rem] lg:w-[7.5rem]">
                                <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="h-full w-full object-contain" loading="eager" decoding="async">
                            </div>
                        @endif

                        <div class="min-w-0">
                            @if ($this->eyebrow())
                                <p class="text-xs font-semibold uppercase leading-5 tracking-wide text-[color:var(--niva-primary-100)] sm:text-sm">{{ $this->eyebrow() }}</p>
                            @endif
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-white sm:mt-3 sm:text-5xl">{{ $this->title() }}</h1>
                            @if ($this->subtitle())
                                <p class="mt-4 cx-public-copy-narrow text-base leading-7 text-zinc-100 sm:mt-5 sm:text-lg">{{ $this->subtitle() }}</p>
                            @endif
                            @if ($ctaItems !== [])
                                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    @foreach ($ctaItems as $cta)
                                        @php($ctaClass = $darkCtaClass($cta['variant']))
                                        <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($isShowcaseHeader)
            <div @class([
                'relative flex flex-1 overflow-hidden bg-[color:var(--niva-primary-950)] text-white',
                'min-h-[calc(100%-5rem)] lg:min-h-[calc(100%-6rem)]',
            ])>
                @if ($headerImageUrl)
                    <x-public.optimized-picture
                        :sources="$headerImageSources"
                        :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                        :mobile-media="$mobileImageMedia"
                        alt=""
                        img-class="size-full object-cover {{ $mobileImageObjectClass }} lg:[object-position:center_54%] [filter:saturate(1.03)_contrast(1.02)_brightness(1.02)]"
                        class="absolute inset-0 block size-full"
                        loading="eager"
                        fetchpriority="high"
                    />
                @else
                    <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--niva-primary-900) 0%, var(--niva-primary-700) 52%, var(--niva-primary-300) 100%);"></div>
                @endif

                <div class="absolute inset-0" style="background: linear-gradient(90deg, rgba(9,9,11,0.38) 0%, rgba(9,9,11,0.24) 30%, rgba(9,9,11,0.08) 58%, transparent 82%);"></div>
                <div class="absolute inset-x-0 bottom-0 h-1/2" style="background: linear-gradient(0deg, rgba(9,9,11,0.22) 0%, rgba(9,9,11,0.06) 62%, transparent 100%);"></div>

                <div class="relative z-10 mx-auto flex w-full max-w-[92rem] flex-1 items-center px-6 py-14 sm:px-8 lg:px-14 lg:py-18">
                    <div @class([
                        'relative max-w-[46rem] -translate-y-6 [text-shadow:0_2px_20px_rgba(0,0,0,0.46)] sm:-translate-y-8 lg:translate-y-0',
                        'py-8' => ! $this->small,
                        'py-6' => $this->small,
                    ])>
                        @if ($logoUrl)
                            <div @class([
                                'mb-6 flex shrink-0 items-center',
                                'mb-5' => $this->small,
                            ])>
                                <div @class([
                                    'flex shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-white/30',
                                    'h-20 w-20 sm:h-24 sm:w-24 lg:h-28 lg:w-28' => ! $this->small,
                                    'h-16 w-16 sm:h-20 sm:w-20' => $this->small,
                                ])>
                                    <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="h-full w-full object-contain" loading="eager" decoding="async">
                                </div>
                            </div>
                        @endif

                        @if ($this->eyebrow())
                            <div class="max-w-[34rem] text-sm font-semibold uppercase leading-6 tracking-[0.16em] text-white/90">
                                <span>{{ $this->eyebrow() }}</span>
                            </div>
                        @endif

                        <h1 @class([
                            'max-w-[45rem] break-words font-extrabold leading-[1.04] tracking-tight text-white',
                            'mt-8 text-5xl sm:text-6xl lg:text-7xl' => ! $this->small,
                            'mt-6 text-4xl sm:text-5xl' => $this->small,
                        ])>{{ $this->title() }}</h1>

                        @if ($this->subtitle())
                            <p @class([
                                'mt-6 max-w-[38rem] text-base leading-8 text-white/88',
                                'sm:text-xl sm:leading-9' => ! $this->small,
                            ])>{{ $this->subtitle() }}</p>
                        @endif

                        @if ($ctaItems !== [])
                            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                                @foreach ($ctaItems as $cta)
                                    @if ($cta['variant'] === 'primary')
                                        <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="inline-flex min-h-12 cursor-pointer items-center justify-center rounded-lg bg-[color:var(--niva-primary-600)] px-6 py-3 text-base font-semibold text-white shadow-sm shadow-zinc-950/20 transition duration-200 hover:bg-[color:var(--niva-primary-700)]">
                                            {{ $cta['label'] }}
                                        </a>
                                    @else
                                        <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="inline-flex min-h-12 cursor-pointer items-center justify-center rounded-lg border border-white/80 bg-white/5 px-6 py-3 text-base font-semibold text-white backdrop-blur-sm transition duration-200 hover:bg-white/12">
                                            {{ $cta['label'] }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($isGalleryFrameHeader)
            <div class="flex flex-1 flex-col pb-7 pt-5 lg:pb-8 lg:pt-6">
                <div class="niva-gallery-frame-media relative min-h-[17rem] flex-1 overflow-hidden rounded-xl bg-[color:var(--niva-primary-100)] shadow-sm shadow-zinc-950/8 ring-1 ring-zinc-200/70 sm:min-h-[22rem] lg:min-h-[26rem]">
                    @if ($headerImageUrl)
                        <x-public.optimized-picture
                            :sources="$headerImageSources"
                            :mobile-sources="$mobileImageUrl ? $mobileHeaderImageSources : []"
                            :mobile-media="$mobileImageMedia"
                            alt=""
                            img-class="size-full object-cover {{ $mobileImageObjectClass }} lg:[object-position:center_54%] [filter:saturate(1.06)_contrast(1.03)_brightness(1.02)]"
                            class="absolute inset-0 block size-full"
                            loading="eager"
                            fetchpriority="high"
                        />
                    @else
                        <div class="absolute inset-0" style="background: linear-gradient(135deg, var(--niva-primary-100) 0%, var(--niva-primary-200) 52%, var(--niva-primary-50) 100%);"></div>
                    @endif

                    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-1/3 bg-[linear-gradient(0deg,rgba(9,9,11,0.12)_0%,rgba(9,9,11,0)_100%)]"></div>
                </div>

                <div class="grid gap-6 pt-6 sm:pt-7 lg:grid-cols-[minmax(0,1.08fr)_minmax(20rem,0.72fr)] lg:items-end lg:gap-12">
                    <div class="grid min-w-0 gap-5 sm:grid-cols-[auto_minmax(0,1fr)] sm:items-start">
                        @if ($logoUrl)
                            <div class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white shadow-sm shadow-zinc-950/8 ring-1 ring-zinc-200/80 sm:size-24">
                                <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="size-full object-contain" loading="eager" decoding="async">
                            </div>
                        @endif

                        <div class="min-w-0">
                            @if ($this->eyebrow())
                                <p class="max-w-[34rem] text-xs font-bold uppercase leading-5 tracking-[0.14em] text-[color:var(--niva-primary-800)] sm:text-sm">{{ $this->eyebrow() }}</p>
                            @endif

                            <h1 class="mt-2 max-w-[46rem] break-words text-4xl font-extrabold leading-[1.04] tracking-tight text-zinc-950 sm:text-5xl lg:text-6xl">{{ $this->title() }}</h1>
                        </div>
                    </div>

                    <div class="lg:pb-1">
                        @if ($this->subtitle())
                            <p class="max-w-[36rem] text-base leading-8 text-zinc-600 sm:text-lg">{{ $this->subtitle() }}</p>
                        @endif

                        @if ($ctaItems !== [])
                            <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                                @foreach ($ctaItems as $cta)
                                    @php($ctaClass = $lightCtaClass($cta['variant']))
                                    <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($isEditorialHeader)
            <div @class([
                'relative flex flex-1 items-center justify-center font-sans',
                'py-14 lg:py-16' => ! $this->small,
                'py-8' => $this->small,
            ])>
                <div class="relative z-10 mx-auto flex w-full max-w-[48rem] flex-col items-center text-center [text-shadow:0_2px_18px_rgba(0,0,0,0.34)]">
                    @if ($logoUrl)
                        <div class="mb-5 flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-zinc-200/80 sm:h-24 sm:w-24">
                            <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="h-full w-full object-contain" loading="eager" decoding="async">
                        </div>
                    @endif

                    @if ($this->eyebrow())
                        <p class="max-w-[34rem] text-[13px] font-black uppercase leading-5 tracking-[0.12em] text-white/88 sm:text-sm lg:tracking-[0.16em]">{{ $this->eyebrow() }}</p>
                    @endif

                    <h1 class="mt-3 max-w-[44rem] break-words text-5xl font-black leading-[1.02] tracking-tight text-white sm:text-6xl lg:text-[4.6rem]">{{ $this->title() }}</h1>

                    @if ($this->subtitle())
                        <p class="mt-5 max-w-[34rem] text-base font-semibold leading-8 text-white/90 sm:text-lg">{{ $this->subtitle() }}</p>
                    @endif

                    @if ($ctaItems !== [])
                        <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-center">
                            @foreach ($ctaItems as $cta)
                                @php($ctaClass = $darkCtaClass($cta['variant']))
                                <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @elseif ($isSplitHeader)
            <div @class([
                'relative flex flex-1 items-center font-sans',
                'pt-12' => ! $this->small,
                'pt-4' => $this->small,
                'pb-10 lg:pb-6' => ! $this->small,
                'pb-8 lg:pb-6' => $this->small,
            ])>
                <div @class([
                    'relative z-10 w-full max-w-[33rem] rounded-[1.35rem] bg-white/[0.58] p-4 text-zinc-950 shadow-[0_18px_55px_rgba(255,255,255,0.22)] ring-1 ring-white/60 backdrop-blur-[1px] sm:max-w-[35rem] sm:p-5 md:max-w-[37rem] lg:max-w-[43rem] lg:rounded-none lg:bg-transparent lg:p-0 lg:pr-8 lg:shadow-none lg:ring-0 lg:backdrop-blur-none',
                    'lg:pt-8' => ! $this->small,
                    'lg:pt-4' => $this->small,
                ])>
                    @if ($logoUrl)
                        <div @class([
                            'mb-4 flex shrink-0 items-center',
                            'mb-3' => $this->small,
                        ])>
                            <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" loading="eager" decoding="async" @class([
                                'object-contain',
                                'h-20 w-20 sm:h-24 sm:w-24 lg:h-[9.25rem] lg:w-[9.25rem]' => ! $this->small,
                                'h-11 w-11 sm:h-12 sm:w-12' => $this->small,
                            ])>
                        </div>
                    @endif

                    @if ($this->eyebrow())
                        <p @class([
                            'max-w-[31rem] font-bold uppercase tracking-[0.08em] text-[color:var(--niva-primary-800)]',
                            'text-[0.8rem] leading-5 sm:text-[0.9rem] sm:leading-6 lg:text-xl lg:leading-8 lg:tracking-wide' => ! $this->small,
                            'text-xs leading-5 sm:text-sm sm:leading-6' => $this->small,
                        ])>{{ $this->eyebrow() }}</p>
                    @endif

                    <h1 @class([
                        'mt-2 cx-public-copy-narrow break-words font-bold leading-tight tracking-tight text-zinc-950',
                        'text-[2.25rem] sm:text-[3rem] lg:text-[5.05rem]' => ! $this->small,
                        'text-[1.7rem] sm:text-[2.1rem] lg:text-[2.35rem]' => $this->small,
                    ])>{{ $this->title() }}</h1>

                    @if ($this->subtitle())
                        <p @class([
                            'mt-3 cx-public-copy-compact leading-8 text-zinc-700',
                            'text-base sm:text-lg lg:text-xl' => ! $this->small,
                            'text-sm sm:text-base' => $this->small,
                        ])>{{ $this->subtitle() }}</p>
                    @endif

                    @if ($ctaItems !== [])
                        <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @foreach ($ctaItems as $cta)
                                @php($ctaClass = $lightCtaClass($cta['variant']))
                                <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>
        @elseif ($isCraftHeader)
            <div @class([
                'relative flex flex-1 items-center font-sans',
                'pt-14' => ! $this->small,
                'pt-10' => $this->small,
                'pb-10 lg:pb-8' => ! $this->small,
                'pb-8 lg:pb-8' => $this->small,
            ])>
                <div class="relative z-10 w-full max-w-[32rem] rounded-[1.5rem] bg-[#fffaf1]/[0.66] p-4 text-zinc-950 shadow-[0_18px_55px_rgba(120,78,40,0.12)] ring-1 ring-white/60 backdrop-blur-[1px] sm:max-w-[34rem] sm:p-5 md:max-w-[35rem] lg:w-[42%] lg:max-w-none lg:rounded-none lg:bg-transparent lg:p-0 lg:py-10 lg:pl-8 lg:pr-10 lg:shadow-none lg:ring-0 lg:backdrop-blur-none xl:pr-14">
                    @if ($logoUrl)
                        <div @class([
                            'mb-4 flex shrink-0 items-center',
                            'mb-3' => $this->small,
                        ])>
                            <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" loading="eager" decoding="async" @class([
                                'object-contain',
                                'h-16 w-16 sm:h-20 sm:w-20 lg:h-24 lg:w-24' => ! $this->small,
                                'h-11 w-11 sm:h-12 sm:w-12' => $this->small,
                            ])>
                        </div>
                    @endif

                    @if ($this->eyebrow())
                        <p class="max-w-[30rem] text-[0.75rem] font-bold uppercase leading-5 tracking-[0.1em] text-[color:var(--niva-primary-800)] sm:text-[0.82rem] lg:text-sm lg:tracking-[0.16em]">{{ $this->eyebrow() }}</p>
                    @endif

                    <h1 @class([
                        'mt-3 max-w-[34rem] break-words font-extrabold leading-[1.02] tracking-tight text-zinc-950',
                        'text-[2.35rem] sm:text-[3.15rem] lg:text-7xl' => ! $this->small,
                        'text-4xl sm:text-5xl' => $this->small,
                    ])>{{ $this->title() }}</h1>

                    @if ($this->subtitle())
                        <p @class([
                            'mt-3 max-w-[30rem] text-base leading-8 text-zinc-700',
                            'sm:text-lg' => ! $this->small,
                        ])>{{ $this->subtitle() }}</p>
                    @endif

                    @if ($ctaItems !== [])
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @foreach ($ctaItems as $cta)
                                @php($ctaClass = $softCtaClass($cta['variant']))
                                <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @elseif ($isPrimarySplitHeader)
            <div @class([
                'relative flex flex-1 flex-col font-sans lg:grid lg:grid-cols-[minmax(0,0.78fr)_minmax(0,1.22fr)]',
                'pt-10 lg:pt-0' => ! $this->small,
                'pt-8 lg:pt-0' => $this->small,
            ])>
                <div class="flex min-h-[28rem] items-center py-10 text-white lg:min-h-0 lg:py-0 lg:pr-10 xl:pr-16">
                    <div class="w-full max-w-[36rem]">
                        @if ($logoUrl)
                            <div @class([
                                'mb-6 flex shrink-0 items-center',
                                'mb-5' => $this->small,
                            ])>
                                <div @class([
                                    'flex shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-white/30',
                                    'h-24 w-24 sm:h-28 sm:w-28' => ! $this->small,
                                    'h-20 w-20 sm:h-24 sm:w-24' => $this->small,
                                ])>
                                    <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="h-full w-full object-contain" loading="eager" decoding="async">
                                </div>
                            </div>
                        @endif

                    @if ($this->eyebrow())
                        <p class="max-w-[34rem] text-sm font-semibold uppercase tracking-[0.16em] text-white/90">{{ $this->eyebrow() }}</p>
                    @endif

                    <h1 @class([
                        'mt-5 max-w-[34rem] break-words font-extrabold leading-tight tracking-tight text-white',
                        'text-5xl sm:text-6xl lg:text-7xl' => ! $this->small,
                        'text-4xl sm:text-5xl' => $this->small,
                    ])>{{ $this->title() }}</h1>

                    @if ($this->subtitle())
                        <p @class([
                            'mt-5 max-w-[32rem] text-base leading-8 text-white/85',
                            'sm:text-lg' => ! $this->small,
                        ])>{{ $this->subtitle() }}</p>
                    @endif

                    @if ($ctaItems !== [])
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center">
                            @foreach ($ctaItems as $cta)
                                @php($ctaClass = $primarySplitCtaClass($cta['variant']))
                                <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                            @endforeach
                        </div>
                    @endif
                </div>
                </div>

                <div class="cx-public-mobile-bleed-md h-[18rem] overflow-hidden bg-[color:var(--niva-primary-100)] sm:h-[24rem] lg:hidden">
                    <div class="relative h-full overflow-hidden">
                        @if ($mobileHeaderImageUrl)
                            <x-public.optimized-picture
                                :sources="$mobileHeaderImageSources"
                                alt=""
                                img-class="size-full object-cover {{ $mobileImageObjectClass }} {{ $mobileImageFilterClass }}"
                                class="block size-full"
                                loading="eager"
                                fetchpriority="high"
                            />
                        @else
                            <div class="size-full bg-[color:var(--niva-primary-100)]"></div>
                        @endif
                        <div class="pointer-events-none absolute inset-0 bg-[color:var(--niva-primary)] opacity-5"></div>
                        <div class="pointer-events-none absolute inset-y-0 left-0 w-12 shadow-[inset_20px_0_30px_-28px_rgba(0,0,0,0.45)]"></div>
                    </div>
                </div>
            </div>
        @else
            <div @class([
                'flex flex-1 items-center justify-end pb-8',
                'pt-20' => ! $this->small,
                'pt-16' => $this->small,
            ])>
                <div class="ml-auto max-w-[34rem] rounded-2xl bg-[#fffaf1]/88 p-5 text-zinc-950 shadow-xl shadow-zinc-950/14 ring-1 ring-white/75 backdrop-blur-[1px] sm:max-w-[36rem] sm:p-6 lg:max-w-[35rem] lg:p-7">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:gap-5">
                        @if ($logoUrl)
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white p-0 shadow-sm ring-1 ring-zinc-200 sm:h-24 sm:w-24 lg:h-[6.75rem] lg:w-[6.75rem]">
                                <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="h-full w-full object-contain" loading="eager" decoding="async">
                            </div>
                        @endif

                        <div class="min-w-0">
                            @if ($this->eyebrow())
                                <p class="text-sm font-semibold uppercase tracking-wide text-[color:var(--niva-primary-800)]">{{ $this->eyebrow() }}</p>
                            @endif
                            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-zinc-950 sm:text-4xl">{{ $this->title() }}</h1>
                            @if ($this->subtitle())
                                <p class="mt-5 cx-public-copy-narrow text-base leading-7 text-zinc-700 sm:text-lg">{{ $this->subtitle() }}</p>
                            @endif
                            @if ($ctaItems !== [])
                                <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    @foreach ($ctaItems as $cta)
                                        @php($ctaClass = $lightCtaClass($cta['variant']))
                                        <a href="{{ $cta['href'] }}" @if (($cta['target'] ?? '_self') === '_blank') target="_blank" rel="noopener noreferrer" @endif class="{{ $ctaClass }}">{{ $cta['label'] }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @endif
    </div>
</header>
