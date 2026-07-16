        <?php if (! in_array($type, ['statistics', 'features', 'partners', 'gallery', 'gallery_grid', 'photo_gallery', 'video', 'faq', 'testimonials', 'featured_products', 'all_products', 'featured_news', 'latest_news', 'taxonomy_news', 'how_to_order', 'about', 'mission', 'vision', 'values', 'team', 'contact', 'social_links'], true)) { ?>
            @php
                $sectionImage = $assetUrl($image);
            @endphp

            @if ($sectionImage)
                <img src="{{ $sectionImage }}" alt="" class="cx-public-section-content aspect-[4/3] w-full rounded-xl object-cover shadow-sm shadow-zinc-950/10" loading="lazy" decoding="async">
            @elseif ($items->isNotEmpty())
                <div class="cx-public-section-content cx-public-grid-compact">
                    @foreach ($items as $item)
                        <article class="cx-public-surface cx-public-card-padding dark:bg-zinc-900 dark:shadow-black/20">
                            <h3 class="font-semibold text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                            @if ($item->localized('description') || $item->localized('content'))
                                <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <x-public-section-empty-state
                    :section="$section"
                    class="cx-public-section-content"
                    icon="document-text"
                    :title="__('Sadržaj uskoro')"
                    :description="__('Sadržaj ove sekcije prikazat će se ovdje kada bude spreman za objavu.')"
                    compact
                />
            @endif
        <?php } ?>
