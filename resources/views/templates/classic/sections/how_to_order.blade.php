        <?php if ($type === 'how_to_order') {
            $orderLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
            $orderLayout = in_array($orderLayout, ['cards', 'showcase', 'journal'], true) ? $orderLayout : 'cards';
            ?>

            <?php if ($orderLayout === 'showcase') { ?>
                <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                    <div class="cx-public-grid md:grid-cols-2 lg:grid-cols-3">
                        <?php foreach ($items as $index => $item) {
                            $stepLabel = $item->setting('value') ?: __('Korak').' '.($index + 1);
                            ?>
                            <article class="flex h-full flex-col cx-public-surface-plain cx-public-card-padding cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="cx-public-badge-sm">{{ $stepLabel }}</span>
                                    <?php if ($item->icon) { ?>
                                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-[color:var(--niva-primary-700)] shadow-sm ring-1 ring-zinc-200/70 dark:bg-zinc-900 dark:text-[color:var(--niva-primary-300)] dark:ring-zinc-800">
                                            <flux:icon :name="$item->icon" class="size-5" />
                                        </span>
                                    <?php } ?>
                                </div>
                                <h3 class="mt-5 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                <?php if ($item->localized('description') || $item->localized('content')) { ?>
                                    <p class="mt-3 flex-1 cx-public-item-text text-zinc-700 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                                <?php } ?>
                            </article>
                        <?php } ?>
                    </div>
                </div>
            <?php } elseif ($orderLayout === 'journal') { ?>
                <div class="cx-public-section-content cx-public-stack">
                    <?php foreach ($items as $index => $item) {
                        $stepLabel = $item->setting('value') ?: __('Korak').' '.($index + 1);
                        ?>
                        <article class="cx-public-grid-compact cx-public-surface cx-public-card-padding-compact dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20 sm:grid-cols-[8rem_1fr] sm:items-center">
                            <div class="flex items-center gap-3 sm:flex-col sm:items-start">
                                <span class="cx-public-badge-sm">{{ $stepLabel }}</span>
                                <?php if ($item->icon) { ?>
                                    <span class="inline-flex size-10 items-center justify-center rounded-full bg-zinc-50 text-[color:var(--niva-primary-700)] dark:bg-zinc-900 dark:text-[color:var(--niva-primary-300)]">
                                        <flux:icon :name="$item->icon" class="size-5" />
                                    </span>
                                <?php } ?>
                            </div>
                            <div>
                                <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                                <?php if ($item->localized('description') || $item->localized('content')) { ?>
                                    <p class="mt-2 cx-public-item-text text-zinc-700 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="cx-public-section-content cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                    <?php foreach ($items as $index => $item) {
                        $stepLabel = $item->setting('value') ?: __('Korak').' '.($index + 1);
                        ?>
                        <article class="flex h-full flex-col cx-public-surface cx-public-card-padding-loose cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                            <div class="flex items-center justify-between gap-4">
                                <?php if ($item->icon) { ?>
                                    <span class="inline-flex size-11 items-center justify-center rounded-full bg-[color:var(--niva-primary-50)] text-[color:var(--niva-primary-700)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)]">
                                        <flux:icon :name="$item->icon" class="size-5" />
                                    </span>
                                <?php } ?>
                                <span class="cx-public-badge-muted-sm">{{ $stepLabel }}</span>
                            </div>
                            <h3 class="mt-5 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $item->localized('title') }}</h3>
                            <?php if ($item->localized('description') || $item->localized('content')) { ?>
                                <p class="mt-3 flex-1 cx-public-item-text text-zinc-700 dark:text-zinc-300">{{ $item->localized('description') ?: $item->localized('content') }}</p>
                            <?php } ?>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
