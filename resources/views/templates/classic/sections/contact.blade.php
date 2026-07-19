        <?php if ($type === 'contact') { ?>
            @php
                $normalizeContactEntry = function (string $label, mixed $value, ?string $icon = null) {
                    $value = is_string($value) ? trim($value) : $value;
                    $icon = trim((string) $icon) ?: 'information-circle';
                    $normalized = str($label.' '.$icon)->lower()->toString();
                    $key = match (true) {
                        $icon === 'envelope' || filter_var((string) $value, FILTER_VALIDATE_EMAIL) !== false => 'email',
                        $icon === 'phone' || str_contains($normalized, 'telefon') => 'phone',
                        default => str($label)->slug('_')->toString(),
                    };
                    $isIntro = $icon === 'document-text'
                        || str_contains($normalized, 'opis')
                        || str_contains($normalized, 'lokacija')
                        || str_contains($normalized, 'upute');

                    return [
                        'key' => $key,
                        'label' => $label !== '' ? $label : __('Kontakt'),
                        'icon' => $icon,
                        'value' => $value,
                        'is_intro' => $isIntro,
                    ];
                };
                $contact = (array) data_get($section->settings, 'contact', []);
                $itemContactEntries = $items
                    ->filter(fn ($item) => filled($item->localized('content')) || filled($item->localized('description')) || filled($item->url))
                    ->map(fn ($item) => $normalizeContactEntry(
                        trim((string) $item->localized('title')),
                        $item->localized('content') ?: $item->localized('description') ?: $item->url,
                        $item->icon,
                    ))
                    ->values();
                $legacyContactEntries = collect([
                    ['key' => 'institution_name', 'label' => __('Ustanova'), 'icon' => 'academic-cap', 'intro' => false],
                    ['key' => 'cooperative_name', 'label' => __('Udruga'), 'icon' => 'building-office-2', 'intro' => false],
                    ['key' => 'address', 'label' => __('Adresa'), 'icon' => 'map-pin', 'intro' => false],
                    ['key' => 'city', 'label' => __('Mjesto'), 'icon' => 'map', 'intro' => false],
                    ['key' => 'email', 'label' => __('E-pošta'), 'icon' => 'envelope', 'intro' => false],
                    ['key' => 'phone', 'label' => __('Telefon'), 'icon' => 'phone', 'intro' => false],
                    ['key' => 'leader_name', 'label' => __('Voditelj'), 'icon' => 'user', 'intro' => false],
                    ['key' => 'working_hours', 'label' => __('Vrijeme za kontakt'), 'icon' => 'clock', 'intro' => false],
                    ['key' => 'location', 'label' => __('Lokacija ili upute'), 'icon' => 'map', 'intro' => true],
                    ['key' => 'description', 'label' => __('Kratak opis'), 'icon' => 'document-text', 'intro' => true],
                ])->map(function ($card) use ($contact, $normalizeContactEntry) {
                    $entry = $normalizeContactEntry($card['label'], data_get($contact, $card['key']), $card['icon']);
                    $entry['is_intro'] = (bool) $card['intro'];

                    return $entry;
                })->filter(fn ($card) => filled($card['value']))->values();
                $contactEntries = $itemContactEntries->isNotEmpty() ? $itemContactEntries : $legacyContactEntries;
                $contactIntro = $contactEntries->filter(fn ($card) => (bool) $card['is_intro'])->values();
                $contactCards = $contactEntries->reject(fn ($card) => (bool) $card['is_intro'])->values();

                if ($contactCards->isEmpty()) {
                    $contactCards = $contactEntries;
                    $contactIntro = collect();
                }

                $contactDescription = data_get($contactIntro->first(fn ($card) => $card['key'] === 'kratak_opis' || $card['icon'] === 'document-text'), 'value');
                $contactLocation = data_get($contactIntro->first(fn ($card) => $card['key'] !== 'kratak_opis' && $card['icon'] !== 'document-text'), 'value');
                $contactSettings = (array) $section->settings;
                $hasContactMessageDescription = array_key_exists('contact_message_description', $contactSettings);
                $hasContactMessageLocation = array_key_exists('contact_message_location', $contactSettings);
                $contactMessageTitle = trim((string) data_get($contactSettings, 'contact_message_title', ''));
                $contactMessageTitle = $contactMessageTitle !== '' ? $contactMessageTitle : __('Javite nam se');

                if ($hasContactMessageDescription) {
                    $contactDescription = trim((string) data_get($contactSettings, 'contact_message_description')) ?: null;
                }

                if ($hasContactMessageLocation) {
                    $contactLocation = trim((string) data_get($contactSettings, 'contact_message_location')) ?: null;
                }

                $contactLayout = (string) data_get($section->settings, 'layout_variant', 'split');
                $contactLayout = in_array($contactLayout, ['split', 'cards', 'letter'], true) ? $contactLayout : 'split';
            @endphp

            @if ($contactCards->isNotEmpty() || filled($contactDescription) || filled($contactLocation))
                @if ($contactLayout === 'cards')
                    <div class="cx-public-section-content">
                        @if (filled($contactDescription) || filled($contactLocation))
                            <div class="mb-5 cx-public-surface cx-public-card-padding-loose dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                @if ($contactDescription)
                                    <p class="whitespace-pre-line cx-public-body text-zinc-700 dark:text-zinc-300">{{ $contactDescription }}</p>
                                @endif
                                @if ($contactLocation)
                                    <p class="mt-5 whitespace-pre-line cx-public-body text-zinc-600 dark:text-zinc-300">{{ $contactLocation }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($contactCards->isNotEmpty())
                            <div class="cx-public-grid-compact sm:grid-cols-2 lg:grid-cols-4">
                                @foreach ($contactCards as $card)
                                    <article class="cx-public-surface cx-public-card-padding dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                        <div class="mb-4 inline-flex size-10 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                            <flux:icon :name="$card['icon']" class="size-5" />
                                        </div>
                                        <h3 class="cx-public-meta-strong text-zinc-950 dark:text-white">{{ $card['label'] }}</h3>
                                        <p class="mt-2 break-words cx-public-body text-zinc-600 dark:text-zinc-300">
                                            @if ($card['key'] === 'email')
                                                <a href="mailto:{{ $card['value'] }}" class="cursor-pointer font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>
                                            @elseif ($card['key'] === 'phone')
                                                <a href="tel:{{ preg_replace('/\s+/', '', (string) $card['value']) }}" class="cursor-pointer font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>
                                            @else
                                                <span class="whitespace-pre-line">{{ $card['value'] }}</span>
                                            @endif
                                        </p>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <x-public-section-empty-state
                                :section="$section"
                                icon="envelope"
                                :title="__('Kontakt podaci uskoro')"
                                :description="__('Kontakt podaci prikazat će se ovdje kada budu spremni za objavu.')"
                                compact
                            />
                        @endif
                    </div>
                @elseif ($contactLayout === 'letter')
                    <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding-loose dark:bg-zinc-900/80">
                        <div class="cx-public-grid-loose lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                            <div class="cx-public-surface-plain cx-public-card-padding-loose dark:bg-zinc-950 dark:shadow-black/20">
                                <span class="inline-flex size-12 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                    <flux:icon name="envelope" class="size-5" />
                                </span>
                                <h3 class="mt-6 cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">{{ $contactMessageTitle }}</h3>
                                @if ($contactDescription)
                                    <p class="mt-4 whitespace-pre-line cx-public-body text-zinc-700 dark:text-zinc-300">{{ $contactDescription }}</p>
                                @endif
                                @if ($contactLocation)
                                    <p class="mt-5 whitespace-pre-line cx-public-body text-zinc-600 dark:text-zinc-300">{{ $contactLocation }}</p>
                                @endif
                            </div>

                            @if ($contactCards->isNotEmpty())
                                <dl class="cx-public-grid-tight">
                                    @foreach ($contactCards as $card)
                                        <div class="cx-public-surface-plain cx-public-card-padding-compact dark:bg-zinc-950 dark:shadow-black/20">
                                            <dt class="flex items-center gap-2 cx-public-meta-strong text-zinc-950 dark:text-white"><flux:icon :name="$card['icon']" class="size-4 text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]" />{{ $card['label'] }}</dt>
                                            <dd class="mt-2 min-w-0 break-words cx-public-body text-zinc-600 dark:text-zinc-300">@if ($card['key'] === 'email')<a href="mailto:{{ $card['value'] }}" class="cursor-pointer font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>@elseif ($card['key'] === 'phone')<a href="tel:{{ preg_replace('/\s+/', '', (string) $card['value']) }}" class="cursor-pointer font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>@else<span class="whitespace-pre-line">{{ $card['value'] }}</span>@endif</dd>
                                        </div>
                                    @endforeach
                                </dl>
                            @else
                                <x-public-section-empty-state
                                    :section="$section"
                                    class="flex min-h-64 flex-col items-center justify-center"
                                    icon="envelope"
                                    :title="__('Kontakt podaci uskoro')"
                                    :description="__('Kontakt podaci prikazat će se ovdje kada budu spremni za objavu.')"
                                    compact
                                />
                            @endif
                        </div>
                    </div>
                @else
                    <div class="cx-public-section-content overflow-hidden cx-public-surface dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                        <div class="grid gap-0 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)]">
                            <div class="bg-[color:var(--niva-primary-50)] p-6 dark:bg-[color:var(--niva-primary-950)] lg:p-8">
                                <span class="inline-flex size-12 items-center justify-center rounded-full bg-white text-[color:var(--niva-primary-700)] shadow-sm shadow-zinc-950/5 ring-1 ring-[color:var(--niva-primary-100)] dark:bg-zinc-950 dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                    <flux:icon name="envelope" class="size-5" />
                                </span>
                                <h3 class="mt-6 cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">{{ $contactMessageTitle }}</h3>
                                @if ($contactDescription)
                                    <p class="mt-4 whitespace-pre-line cx-public-body text-zinc-700 dark:text-zinc-300">{{ $contactDescription }}</p>
                                @endif
                                @if ($contactLocation)
                                    <p class="mt-5 whitespace-pre-line cx-public-body text-zinc-600 dark:text-zinc-300">{{ $contactLocation }}</p>
                                @endif
                            </div>

                            <div class="p-6 lg:p-8">
                                @if ($contactCards->isNotEmpty())
                                    <dl class="cx-public-stack-compact">
                                        @foreach ($contactCards as $card)
                                            <div class="grid gap-2 rounded-xl bg-zinc-50 p-4 dark:bg-zinc-900/70 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-5">
                                                <dt class="flex items-center gap-2 cx-public-meta-strong text-zinc-950 dark:text-white">
                                                    <flux:icon :name="$card['icon']" class="size-4 text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]" />
                                                    {{ $card['label'] }}
                                                </dt>
                                                <dd class="min-w-0 cx-public-body text-zinc-600 dark:text-zinc-300">
                                                    @if ($card['key'] === 'email')
                                                        <a href="mailto:{{ $card['value'] }}" class="cursor-pointer break-words font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>
                                                    @elseif ($card['key'] === 'phone')
                                                        <a href="tel:{{ preg_replace('/\s+/', '', (string) $card['value']) }}" class="cursor-pointer font-medium text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $card['value'] }}</a>
                                                    @else
                                                        <span class="whitespace-pre-line">{{ $card['value'] }}</span>
                                                    @endif
                                                </dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                @else
                                    <x-public-section-empty-state
                                        :section="$section"
                                        class="flex min-h-64 flex-col items-center justify-center"
                                        icon="envelope"
                                        :title="__('Kontakt podaci uskoro')"
                                        :description="__('Kontakt podaci prikazat će se ovdje kada budu spremni za objavu.')"
                                        compact
                                    />
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <x-public-section-empty-state
                    :section="$section"
                    class="cx-public-section-content"
                    icon="envelope"
                    :title="__('Kontakt podaci uskoro')"
                    :description="__('Kontakt podaci prikazat će se ovdje kada budu spremni za objavu.')"
                    compact
                />
            @endif
        <?php } ?>
