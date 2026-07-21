        <?php if ($type === 'social_links') { ?>
            @php
                $socialLinksResolver = app(\IvanBaric\NivaTemplate\Support\SocialLinks::class);
                $itemSocialLinks = collect($socialLinksResolver->fromSectionItems($items));
                $legacySocialLinks = collect($socialLinksResolver->fromLegacySettings((array) $section->settings));
                $socialLinks = $itemSocialLinks->isNotEmpty() ? $itemSocialLinks : $legacySocialLinks;
                $socialLayout = (string) data_get($section->settings, 'layout_variant', 'cards');
                $socialLayout = in_array($socialLayout, ['cards', 'strip', 'icons'], true) ? $socialLayout : 'cards';
            @endphp

            @if ($socialLinks->isNotEmpty())
                @if ($socialLayout === 'strip')
                    <div class="cx-public-section-content cx-public-surface cx-public-card-padding-compact dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                        <div class="flex flex-wrap gap-3">
                            @foreach ($socialLinks as $socialLink)
                                <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $socialLink['label'] }}" aria-label="{{ $socialLink['label'] }}" class="group inline-flex min-h-12 cursor-pointer items-center gap-3 rounded-xl bg-zinc-50 px-4 py-2 cx-public-item-title-compact text-zinc-900 cx-public-card-hover hover:bg-[color:var(--niva-primary-50)] hover:text-[color:var(--niva-primary-800)] dark:bg-zinc-900 dark:text-zinc-100 dark:hover:bg-[color:var(--niva-primary-950)] dark:hover:text-[color:var(--niva-primary-200)]">
                                        <span class="inline-flex size-8 items-center justify-center rounded-full bg-white text-zinc-700 shadow-sm shadow-zinc-950/5 transition group-hover:text-[color:var(--niva-primary-700)] dark:bg-zinc-950 dark:text-zinc-200 dark:group-hover:text-[color:var(--niva-primary-300)]">
                                            @include('niva-template::templates.classic.partials.social-icon', ['name' => $socialLink['icon'], 'class' => 'size-4'])
                                        </span>
                                        {{ $socialLink['label'] }}
                                    </a>
                            @endforeach
                        </div>
                    </div>
                @elseif ($socialLayout === 'icons')
                    <div class="cx-public-section-content flex flex-wrap justify-center gap-5">
                        @foreach ($socialLinks as $socialLink)
                            <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $socialLink['label'] }}" aria-label="{{ $socialLink['label'] }}" class="group flex w-24 cursor-pointer flex-col items-center gap-3 text-center">
                                    <span class="inline-flex size-16 items-center justify-center cx-public-surface text-zinc-800 cx-public-motion-icon group-hover:-translate-y-0.5 group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-700)] group-hover:shadow-md group-hover:shadow-zinc-950/10 dark:text-zinc-100 dark:group-hover:bg-[color:var(--niva-primary-950)] dark:group-hover:text-[color:var(--niva-primary-300)]">
                                        @include('niva-template::templates.classic.partials.social-icon', ['name' => $socialLink['icon'], 'class' => 'size-6'])
                                    </span>
                                    <span class="cx-public-item-title-compact text-zinc-600 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-zinc-300 dark:group-hover:text-[color:var(--niva-primary-200)]">{{ $socialLink['label'] }}</span>
                                </a>
                        @endforeach
                    </div>
                @else
                    <div class="cx-public-section-content cx-public-grid-tight sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($socialLinks as $socialLink)
                            <a href="{{ $socialLink['url'] }}" target="_blank" rel="noopener noreferrer" title="{{ $socialLink['label'] }}" aria-label="{{ $socialLink['label'] }}" class="group flex cursor-pointer items-center gap-4 cx-public-surface cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                    <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-800 transition group-hover:bg-[color:var(--niva-primary-50)] group-hover:text-[color:var(--niva-primary-700)] dark:bg-zinc-900 dark:text-zinc-200 dark:group-hover:bg-[color:var(--niva-primary-950)] dark:group-hover:text-[color:var(--niva-primary-300)]">
                                        @include('niva-template::templates.classic.partials.social-icon', ['name' => $socialLink['icon'], 'class' => 'size-5'])
                                    </span>
                                    <span>
                                        <span class="block cx-public-item-title-compact text-zinc-950 dark:text-white">{{ $socialLink['label'] }}</span>
                                        <span class="mt-1 block cx-public-small text-zinc-500 dark:text-zinc-400">{{ __('Otvori poveznicu') }}</span>
                                    </span>
                                </a>
                        @endforeach
                    </div>
                @endif
            @endif
        <?php } ?>
