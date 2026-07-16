@php
    $logoUrl = $this->logoUrl();
    $footerDescription = $this->organizationDescription();
    $contactItems = $this->contactItems();
    $canEditFooter = $this->canEditFooter();
    $canCycleFooterDesign = method_exists($this, 'canCycleFooterVariant') && $this->canCycleFooterVariant();
    $footerControlClass = 'inline-flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-full border border-zinc-950/10 bg-white/90 text-zinc-600 shadow-sm shadow-zinc-950/10 backdrop-blur-md transition duration-200 hover:bg-[color:var(--niva-primary-50)] hover:text-[color:var(--niva-primary-800)] focus:outline-none focus:ring-2 focus:ring-[color:var(--niva-primary-200)] focus:ring-offset-2 disabled:cursor-wait disabled:opacity-60';
@endphp

<footer class="relative border-t border-[color:var(--niva-primary-100)] bg-[color:var(--niva-primary-50)]/35 px-6 py-12 text-zinc-800 dark:border-[color:var(--niva-primary-900)] dark:bg-zinc-950 dark:text-zinc-200">
    @if ($canEditFooter || $canCycleFooterDesign)
        <div class="pointer-events-none absolute right-5 top-5 z-20 flex items-center gap-2">
            @if ($canEditFooter)
                <flux:tooltip :content="__('Uredi podnožje')" position="top">
                    <button
                        type="button"
                        wire:click="$dispatch('pages-open-public-template-part-editor', { part: 'footer', editorTab: 'content' })"
                        data-public-template-edit-link="template_footer"
                        class="{{ $footerControlClass }} pointer-events-auto"
                        aria-label="{{ __('Uredi podnožje') }}"
                    >
                        <flux:icon name="pencil" class="size-4" />
                    </button>
                </flux:tooltip>
            @endif

            @if ($canCycleFooterDesign)
                <flux:tooltip :content="__('Prethodni izgled podnožja: :layout', ['layout' => $this->previousFooterVariantLabel()])" position="top">
                    <button
                        type="button"
                        wire:click="cycleFooterVariant('previous')"
                        wire:target="cycleFooterVariant"
                        wire:loading.attr="disabled"
                        class="{{ $footerControlClass }} pointer-events-auto"
                        aria-label="{{ __('Prethodni izgled podnožja') }}"
                    >
                        <flux:icon name="chevron-left" class="size-4" wire:loading.remove wire:target="cycleFooterVariant" />
                        <flux:icon.loading class="size-4" wire:loading wire:target="cycleFooterVariant" />
                    </button>
                </flux:tooltip>

                <flux:tooltip :content="__('Sljedeći izgled podnožja: :layout', ['layout' => $this->nextFooterVariantLabel()])" position="top">
                    <button
                        type="button"
                        wire:click="cycleFooterVariant('next')"
                        wire:target="cycleFooterVariant"
                        wire:loading.attr="disabled"
                        class="{{ $footerControlClass }} pointer-events-auto"
                        aria-label="{{ __('Sljedeći izgled podnožja') }}"
                    >
                        <flux:icon name="chevron-right" class="size-4" wire:loading.remove wire:target="cycleFooterVariant" />
                        <flux:icon.loading class="size-4" wire:loading wire:target="cycleFooterVariant" />
                    </button>
                </flux:tooltip>
            @endif
        </div>
    @endif

    <div class="cx-public-container">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,1.25fr)_minmax(12rem,0.7fr)_minmax(14rem,0.85fr)]">
            <div>
                <a href="{{ $this->organizationUrl() }}" class="inline-flex cursor-pointer items-center gap-4 text-zinc-950 transition hover:text-[color:var(--niva-primary-800)] dark:text-white dark:hover:text-[color:var(--niva-primary-200)]">
                    <span class="flex size-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-[color:var(--niva-primary-700)] shadow-sm shadow-zinc-950/5 ring-1 ring-[color:var(--niva-primary-100)] dark:bg-zinc-900 dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $this->organizationName() }}" class="size-full object-contain" loading="lazy" decoding="async">
                        @else
                            <span class="text-lg font-semibold">{{ str($this->organizationName())->trim()->substr(0, 1)->upper() }}</span>
                        @endif
                    </span>
                    <span>
                        <span class="block text-xl font-semibold leading-7">{{ $this->organizationName() }}</span>
                    </span>
                </a>

                @if ($footerDescription)
                    <p class="mt-5 max-w-md text-base leading-7 text-zinc-600 dark:text-zinc-300">{{ $footerDescription }}</p>
                @endif
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-950 dark:text-white">{{ __('Stranice') }}</h2>

                <nav class="mt-5 grid gap-3 text-base">
                    @foreach ($this->navItems() as $item)
                        @php($children = $item['children'] ?? [])
                        <div>
                            <a href="{{ $item['href'] }}" class="cursor-pointer text-zinc-600 transition hover:text-[color:var(--niva-primary-800)] dark:text-zinc-300 dark:hover:text-[color:var(--niva-primary-200)]">
                                {{ $item['label'] }}
                            </a>

                            @if ($children !== [])
                                <div class="mt-2 grid gap-2 border-l border-zinc-200 pl-3 text-sm dark:border-zinc-700">
                                    @foreach ($children as $child)
                                        <a href="{{ $child['href'] }}" class="cursor-pointer text-zinc-500 transition hover:text-[color:var(--niva-primary-800)] dark:text-zinc-400 dark:hover:text-[color:var(--niva-primary-200)]">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>
            </div>

            <div>
                <h2 class="text-sm font-semibold uppercase tracking-[0.16em] text-zinc-950 dark:text-white">{{ __('Kontakt') }}</h2>

                @if ($contactItems !== [])
                    <dl class="mt-5 space-y-4 text-base">
                        @foreach ($contactItems as $item)
                            <div>
                                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $item['label'] }}</dt>
                                <dd class="mt-1 leading-7 text-zinc-700 dark:text-zinc-300">
                                    @if ($item['href'])
                                        <a href="{{ $item['href'] }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $item['value'] }}</a>
                                    @else
                                        {{ $item['value'] }}
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @else
                    <p class="mt-5 text-base leading-7 text-zinc-600 dark:text-zinc-300">{{ __('Kontakt podaci bit će prikazani kada budu uneseni u postavkama organizacije.') }}</p>
                @endif
            </div>
        </div>

        <div class="mt-10 flex flex-col gap-3 border-t border-[color:var(--niva-primary-100)] pt-6 text-sm text-zinc-500 dark:border-zinc-800 dark:text-zinc-400 sm:flex-row sm:items-center sm:justify-between">
            <p>{{ $this->copyrightText() }}</p>
            <p>{{ __('Izrađeno u Niva sustavu') }}</p>
        </div>
    </div>
</footer>
