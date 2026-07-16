        <?php if (in_array($type, ['featured_products', 'all_products', 'featured_news', 'latest_news', 'taxonomy_news'], true)) { ?>
            @php
                $displayRecords = $records->isNotEmpty() ? $records : $items;
                $isNewsSection = in_array($type, ['featured_news', 'latest_news', 'taxonomy_news'], true);
                $newsLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $newsLayout = in_array($newsLayout, ['cards', 'featured', 'stacked', 'journal', 'blog_grid', 'image_cards', 'editorial_list'], true) ? $newsLayout : 'cards';
                $newsReadMoreLabel = trim((string) data_get($section, 'settings.read_more_label', ''));
                $newsReadMoreLabel = $newsReadMoreLabel !== '' ? $newsReadMoreLabel : __('Pročitaj objavu');
                $showNewsAuthor = (bool) data_get($section, 'settings.show_author', false);
                $showNewsDate = (bool) data_get($section, 'settings.show_date', false);
                $newsAuthorFor = function ($record): ?string {
                    $author = data_get($record, 'author.name')
                        ?: data_get($record, 'meta.author_name')
                        ?: data_get($record, 'meta.author')
                        ?: data_get($record, 'meta.created_by')
                        ?: data_get($record, 'meta.writer');

                    return filled($author) ? (string) $author : null;
                };
                $newsPublishedDateFor = function ($record): ?string {
                    if (data_get($record, 'published_at')) {
                        $publishedAt = data_get($record, 'published_at');
                        $publishedAt = $publishedAt instanceof \Illuminate\Support\Carbon
                            ? $publishedAt
                            : \Illuminate\Support\Carbon::parse($publishedAt);
                        $months = [
                            1 => 'siječnja',
                            2 => 'veljače',
                            3 => 'ožujka',
                            4 => 'travnja',
                            5 => 'svibnja',
                            6 => 'lipnja',
                            7 => 'srpnja',
                            8 => 'kolovoza',
                            9 => 'rujna',
                            10 => 'listopada',
                            11 => 'studenoga',
                            12 => 'prosinca',
                        ];

                        return $publishedAt->format('j.').' '.$months[(int) $publishedAt->format('n')].' '.$publishedAt->format('Y.');
                    }

                    return null;
                };
                $newsMetaFor = function ($record) use ($showNewsAuthor, $showNewsDate, $newsAuthorFor, $newsPublishedDateFor) {
                    $meta = [];

                    if ($showNewsAuthor && ($author = $newsAuthorFor($record))) {
                        $meta[] = $author;
                    }

                    if ($showNewsDate && ($publishedDate = $newsPublishedDateFor($record))) {
                        $meta[] = $publishedDate;
                    }

                    return $meta;
                };
                $productLayout = (string) data_get($section, 'settings.layout_variant', 'cards');
                $productLayout = in_array($productLayout, ['cards', 'highlighted', 'showcase', 'catalog', 'carousel', 'store_grid', 'scroll_showcase'], true) ? $productLayout : 'cards';
                $productCarouselName = 'products-carousel-'.(string) data_get($section, 'uuid', data_get($section, 'id', 'section'));
                $hideLoadMoreForProductCarousel = ! $isNewsSection && $productLayout === 'carousel';
                $productCarouselHasMore = ! $isNewsSection && $productLayout === 'carousel' && $this->hasMoreCarouselItems($type);
                $showProductPriceSetting = data_get($section, 'settings.show_price');
                $showProductPrice = $showProductPriceSetting === null
                    ? true
                    : (filter_var($showProductPriceSetting, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false);
                $productPriceFor = function ($record) use ($showProductPrice): ?string {
                    if (! $showProductPrice) {
                        return null;
                    }

                    $price = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $record->price : data_get($record, 'price');

                    if ($price === null || $price === '') {
                        return null;
                    }

                    return number_format((float) $price, 2, ',', '.').' €';
                };
            @endphp

            @if ($postFiltersEnabled ?? false)
                <div
                    wire:loading.flex
                    wire:target="selectPostCategoryFilter,clearPostCategoryFilter,clearPostTagFilter,clearPostFilters"
                    class="mx-auto mt-6 w-fit cx-public-badge-muted"
                >
                    <flux:icon.loading class="size-4 text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]" />
                    {{ __('Učitavanje') }}
                </div>
            @endif

            <div
                @if ($postFiltersEnabled ?? false)
                    wire:loading.class="opacity-50"
                    wire:target="selectPostCategoryFilter,clearPostCategoryFilter,clearPostTagFilter,clearPostFilters"
                @endif
                class="transition-opacity duration-200"
            >
                @if ($displayRecords->isEmpty())
                    @php
                        $recordsEmptyTitle = $isNewsSection && ($hasActivePostFilter ?? false)
                            ? __('Nema objava za odabrani filter.')
                            : ($isNewsSection ? __('Objave će se prikazati ovdje.') : __('Radovi će se prikazati ovdje.'));
                        $recordsEmptyDescription = $isNewsSection && ($hasActivePostFilter ?? false)
                            ? __('Promijenite filter za širi prikaz objava.')
                            : __('Kada sadržaj bude objavljen, pojavit će se u ovoj sekciji.');
                        $recordsEmptyIcon = $isNewsSection ? 'newspaper' : 'cube';
                    @endphp

                    <x-public-section-empty-state
                        :section="$section"
                        class="cx-public-section-content"
                        :icon="$recordsEmptyIcon"
                        :title="$recordsEmptyTitle"
                        :description="$recordsEmptyDescription"
                    />
                @else
                    @if (! $isNewsSection && $productLayout === 'carousel')
                    <div class="mt-6">
                        @if ($displayRecords->count() > 1 || $productCarouselHasMore)
                            @include('niva-template::templates.classic.partials.carousel-controls', [
                                'name' => $productCarouselName,
                                'loadTarget' => $type,
                                'hasMore' => $productCarouselHasMore,
                            ])
                        @endif

                        <flux:carousel name="{{ $productCarouselName }}" class="-mx-4" :arrows="false" fade advance="page" track:class="px-4 scroll-px-4">
                            @foreach ($displayRecords as $record)
                                @php
                                    $recordTitle = is_array($record) ? data_get($record, 'title') : (method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title'));
                                    $recordDescriptionSource = is_array($record) ? data_get($record, 'description') : (method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description'));
                                    $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                    $recordImage = is_array($record) ? data_get($record, 'image') : (method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image')));
                                    $recordUrl = is_array($record) ? data_get($record, 'url') : (\IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null);
                                    $recordPrice = $productPriceFor($record);
                                @endphp

                                <flux:carousel.slide class="w-4/5 sm:w-1/2 lg:w-1/3" wire:key="product-carousel-{{ data_get($record, 'id', $loop->index) }}">
                                    <article class="group flex h-full flex-col overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                        <div class="relative overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                            @if ($recordImage)
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                                    </a>
                                                @else
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                                @endif
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col p-5">
                                            <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                                @else
                                                    {{ $recordTitle }}
                                                @endif
                                            </h3>

                                            @if ($recordDescription)
                                                <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                            @endif

                                            @if ($recordPrice)
                                                <p class="mt-5 cx-public-item-title text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                            @endif
                                        </div>
                                    </article>
                                </flux:carousel.slide>
                            @endforeach
                        </flux:carousel>
                    </div>
                @elseif (! $isNewsSection && $productLayout === 'store_grid')
                    <div class="cx-public-section-content mx-auto grid max-w-6xl auto-rows-fr grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(120)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                $recordPrice = $productPriceFor($record);
                            @endphp

                            <article wire:key="product-store-grid-{{ data_get($record, 'id', $loop->index) }}" class="h-full">
                                @if ($recordUrl)
                                    <a href="{{ $recordUrl }}" class="group flex h-full cursor-pointer flex-col overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                @else
                                    <div class="flex h-full flex-col overflow-hidden cx-public-surface dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                @endif

                                <div class="overflow-hidden bg-zinc-100 dark:bg-zinc-900">
                                    @if ($recordImage)
                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col p-5">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <h3 class="line-clamp-2 cx-public-item-title-compact text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">
                                                {{ $recordTitle }}
                                            </h3>
                                        </div>

                                        @if ($recordPrice)
                                            <p class="shrink-0 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                        @endif
                                    </div>

                                    @if ($recordDescription)
                                        <p class="mt-2 line-clamp-2 cx-public-small text-zinc-500 dark:text-zinc-400">{{ $recordDescription }}</p>
                                    @endif
                                </div>

                                @if ($recordUrl)
                                    </a>
                                @else
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @elseif (! $isNewsSection && $productLayout === 'scroll_showcase')
                    <div class="cx-public-section-content relative">
                        <div class="relative -mb-6 w-full overflow-x-auto pb-6">
                            <ul role="list" class="inline-flex gap-x-8 lg:grid lg:w-full lg:grid-cols-4 lg:gap-x-8">
                                @foreach ($displayRecords as $record)
                                    @php
                                        $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                        $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                        $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(70)->toString() : null;
                                        $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                        $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                        $recordPrice = $productPriceFor($record);
                                    @endphp

                                    <li wire:key="product-scroll-showcase-{{ data_get($record, 'id', $loop->index) }}" class="inline-flex w-64 shrink-0 flex-col text-center lg:w-auto">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="group block cursor-pointer transition duration-200 hover:-translate-y-0.5" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                        @endif

                                        <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                            @if ($recordImage)
                                                <img src="{{ $recordImage }}" alt="" class="aspect-square w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-square w-full" icon="cube" icon-class="size-9" />
                                            @endif
                                        </div>

                                        <div class="mt-6">
                                            @if ($recordDescription)
                                                <p class="cx-public-small text-zinc-500 dark:text-zinc-400">{{ $recordDescription }}</p>
                                            @endif
                                            <h3 class="mt-1 cx-public-meta-strong text-zinc-950 transition group-hover:text-[color:var(--niva-primary-800)] dark:text-white dark:group-hover:text-[color:var(--niva-primary-200)]">
                                                {{ $recordTitle }}
                                            </h3>
                                            @if ($recordPrice)
                                                <p class="mt-1 cx-public-meta-strong text-zinc-950 dark:text-white">{{ $recordPrice }}</p>
                                            @endif
                                        </div>

                                        @if ($recordUrl)
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @elseif (! $isNewsSection && $productLayout === 'highlighted')
                    @php
                        $featuredRecord = $displayRecords->first();
                        $sideRecords = $displayRecords->slice(1, 2)->values();
                        $remainingRecords = $displayRecords->slice(3)->values();
                    @endphp

                    <div class="cx-public-section-content">
                        <div class="cx-public-grid-loose lg:grid-cols-[minmax(0,1fr)_minmax(0,1.16fr)] lg:items-stretch">
                            @if ($featuredRecord)
                                @php
                                    $recordTitle = method_exists($featuredRecord, 'localized') ? $featuredRecord->localized('title') : data_get($featuredRecord, 'title');
                                    $recordDescriptionSource = method_exists($featuredRecord, 'localized') ? $featuredRecord->localized('description') : data_get($featuredRecord, 'description');
                                    $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                    $recordImage = method_exists($featuredRecord, 'featuredImageUrl') ? $featuredRecord->featuredImageUrl() : $assetUrl(data_get($featuredRecord, 'image') ?: data_get($featuredRecord, 'featured_image'));
                                    $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($featuredRecord) ? $this->productContentUrl((string) ($featuredRecord->slug ?: $featuredRecord->uuid)) : null;
                                    $recordPrice = $productPriceFor($featuredRecord);
                                @endphp

                                <article class="overflow-hidden cx-public-surface-plain cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                                    <div class="bg-zinc-100 dark:bg-zinc-900">
                                        @if ($recordImage)
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                            @endif
                                        @else
                                            <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                        @endif
                                    </div>

                                    <div class="p-6">
                                        <h3 class="cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if ($recordDescription)
                                            <p class="mt-4 cx-public-body text-zinc-700 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                        @if ($recordPrice)
                                            <p class="mt-5 cx-public-item-title text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endif

                            @if ($sideRecords->isNotEmpty())
                                <div class="cx-public-grid-loose">
                                    @foreach ($sideRecords as $record)
                                        @php
                                            $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                            $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                            $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                            $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                            $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                            $recordPrice = $productPriceFor($record);
                                        @endphp

                                        <article class="cx-public-grid cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover sm:grid-cols-[minmax(12rem,0.86fr)_minmax(0,1fr)] sm:items-center dark:bg-zinc-950 dark:shadow-black/20">
                                            <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                                @if ($recordImage)
                                                    @if ($recordUrl)
                                                        <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                            <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                        </a>
                                                    @else
                                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                    @endif
                                                @else
                                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                                @endif
                                            </div>

                                            <div>
                                                <h3 class="cx-public-item-title text-zinc-950 dark:text-white">
                                                    @if ($recordUrl)
                                                        <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                                    @else
                                                        {{ $recordTitle }}
                                                    @endif
                                                </h3>
                                                @if ($recordDescription)
                                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                                @endif
                                                @if ($recordPrice)
                                                    <p class="mt-4 cx-public-item-title text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                                @endif
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($remainingRecords->isNotEmpty())
                            <div class="cx-public-section-content-compact cx-public-grid-loose sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($remainingRecords as $record)
                                    @php
                                        $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                        $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                        $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                        $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                        $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                        $recordPrice = $productPriceFor($record);
                                    @endphp

                                    <article class="cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                                        <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                            @if ($recordImage)
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                    </a>
                                                @else
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                @endif
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                            @endif
                                        </div>
                                        <h3 class="mt-5 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if ($recordDescription)
                                            <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                        @if ($recordPrice)
                                            <p class="mt-4 cx-public-item-title tracking-tight text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif (! $isNewsSection && $productLayout === 'showcase')
                    <div class="cx-public-section-content cx-public-surface-band cx-public-band-padding dark:bg-zinc-900/80">
                        <div class="cx-public-grid sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($displayRecords as $record)
                                @php
                                    $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                    $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                    $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                    $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                    $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                    $recordPrice = $productPriceFor($record);
                                @endphp
                                <article class="group flex h-full flex-col overflow-hidden cx-public-surface-plain p-3 cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                                    <div class="relative overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                        @if ($recordImage)
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            @endif
                                        @else
                                            <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" icon-class="size-10" />
                                        @endif
                                        @if ($recordPrice)
                                            <span class="absolute right-3 top-3 cx-public-badge-inverse">
                                                {{ $recordPrice }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex flex-1 flex-col px-2 pb-2 pt-4">
                                        <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if ($recordDescription)
                                            <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @elseif (! $isNewsSection && $productLayout === 'catalog')
                    <div class="cx-public-section-content cx-public-stack">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                $recordPrice = $productPriceFor($record);
                            @endphp
                            <article class="cx-public-grid cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover sm:grid-cols-[12rem_minmax(0,1fr)_auto] sm:items-center sm:gap-8 dark:bg-zinc-950 dark:shadow-black/20 lg:grid-cols-[15rem_minmax(0,1fr)_auto]">
                                <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                    @if ($recordImage)
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="cx-public-item-title text-zinc-950 dark:text-white">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                        @else
                                            {{ $recordTitle }}
                                        @endif
                                    </h3>
                                    @if ($recordDescription)
                                        <p class="mt-3 cx-public-copy-narrow cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                    @endif
                                </div>
                                @if ($recordPrice)
                                    <p class="cx-public-item-title text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)] sm:justify-self-end">{{ $recordPrice }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @elseif (! $isNewsSection)
                    <div class="cx-public-section-content cx-public-grid sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? $record->localized('description') : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) ? $this->productContentUrl((string) ($record->slug ?: $record->uuid)) : null;
                                $recordPrice = $productPriceFor($record);
                            @endphp
                            <article class="flex h-full flex-col overflow-hidden cx-public-surface cx-public-card-hover dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20">
                                <div class="relative bg-zinc-100 dark:bg-zinc-900">
                                    @if ($recordImage)
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" icon-class="size-9" />
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col p-5">
                                    <div class="flex flex-1 flex-col">
                                        <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if ($recordDescription)
                                            <p class="mt-3 flex-1 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </div>

                                    @if ($recordPrice)
                                        <p class="mt-5 cx-public-item-title text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ $recordPrice }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'featured')
                    @php
                        $featuredRecord = $displayRecords->first();
                        $sideRecords = $displayRecords->slice(1, 2)->values();
                        $remainingRecords = $displayRecords->slice(3)->values();
                    @endphp

                    <div class="cx-public-section-content">
                        <div class="grid gap-8 lg:grid-cols-[minmax(0,1.12fr)_minmax(0,0.88fr)] lg:gap-8">
                            @if ($featuredRecord)
                                @php
                                    $recordTitle = method_exists($featuredRecord, 'localized') ? $featuredRecord->localized('title') : data_get($featuredRecord, 'title');
                                    $recordDescriptionSource = method_exists($featuredRecord, 'localized')
                                        ? ($featuredRecord->localized('excerpt') ?: $featuredRecord->localized('content'))
                                        : data_get($featuredRecord, 'description');
                                    $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(190)->toString() : null;
                                    $recordImage = method_exists($featuredRecord, 'featuredImageUrl') ? $featuredRecord->featuredImageUrl() : $assetUrl(data_get($featuredRecord, 'image') ?: data_get($featuredRecord, 'featured_image'));
                                    $recordUrl = $featuredRecord instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $featuredRecord->slug) : null;
                                    $recordMeta = $newsMetaFor($featuredRecord);
                                @endphp

                                <article wire:key="news-featured-{{ $featuredRecord->getKey() ?? $featuredRecord->slug ?? $recordTitle }}">
                                    <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                        @if ($recordImage)
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[16/9] w-full object-cover" loading="lazy" decoding="async">
                                            @endif
                                        @else
                                            <x-corexis::public-image-placeholder class="aspect-[16/9] w-full" icon="cube" />
                                        @endif
                                    </div>
                                    <div class="pt-6">
                                        <h3 class="cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if (! empty($recordMeta))
                                            <p class="mt-3 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                        @endif
                                        @if ($recordDescription)
                                            <p class="mt-4 cx-public-body text-zinc-700 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="mt-5 inline-flex cursor-pointer items-center gap-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                {{ $newsReadMoreLabel }}
                                                <flux:icon name="arrow-right" class="size-4" />
                                            </a>
                                        @endif
                                    </div>
                                </article>
                            @endif

                            @if ($sideRecords->isNotEmpty())
                                <div class="cx-public-grid">
                                    @foreach ($sideRecords as $record)
                                        @php
                                            $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                            $recordDescriptionSource = method_exists($record, 'localized')
                                                ? ($record->localized('excerpt') ?: $record->localized('content'))
                                                : data_get($record, 'description');
                                            $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(120)->toString() : null;
                                            $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                            $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                            $recordMeta = $newsMetaFor($record);
                                        @endphp

                                        <article wire:key="news-featured-side-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20">
                                            <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                                @else
                                                    {{ $recordTitle }}
                                                @endif
                                            </h3>
                                            @if (! empty($recordMeta))
                                                <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                            @endif
                                            <div class="mt-4 cx-public-grid sm:grid-cols-[minmax(12rem,0.84fr)_minmax(0,1fr)] sm:items-center">
                                                <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                                    @if ($recordImage)
                                                        @if ($recordUrl)
                                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                            </a>
                                                        @else
                                                            <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                        @endif
                                                    @else
                                                        <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="cube" />
                                                    @endif
                                                </div>
                                                <div>
                                                @if ($recordDescription)
                                                    <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                                @endif
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="mt-4 inline-flex cursor-pointer items-center gap-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                        {{ $newsReadMoreLabel }}
                                                        <flux:icon name="arrow-right" class="size-4" />
                                                    </a>
                                                @endif
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if ($remainingRecords->isNotEmpty())
                            <div class="cx-public-section-content-compact cx-public-grid-loose sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($remainingRecords as $record)
                                    @php
                                        $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                        $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                        $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(110)->toString() : null;
                                        $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                        $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                        $recordMeta = $newsMetaFor($record);
                                    @endphp
                                    <article wire:key="news-featured-more-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}">
                                        @if ($recordImage)
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                            @endif
                                        @endif
                                        <h3 class="mt-4 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if (! empty($recordMeta))
                                            <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                        @endif
                                        @if ($recordDescription)
                                            <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'stacked')
                    @php
                        $featuredRecord = $displayRecords->first();
                        $lowerRecords = $displayRecords->slice(1, 2)->values();
                        $remainingRecords = $displayRecords->slice(3)->values();
                    @endphp

                    <div class="cx-public-section-content">
                        @if ($featuredRecord)
                            @php
                                $recordTitle = method_exists($featuredRecord, 'localized') ? $featuredRecord->localized('title') : data_get($featuredRecord, 'title');
                                $recordDescriptionSource = method_exists($featuredRecord, 'localized')
                                    ? ($featuredRecord->localized('excerpt') ?: $featuredRecord->localized('content'))
                                    : data_get($featuredRecord, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(190)->toString() : null;
                                $recordImage = method_exists($featuredRecord, 'featuredImageUrl') ? $featuredRecord->featuredImageUrl() : $assetUrl(data_get($featuredRecord, 'image') ?: data_get($featuredRecord, 'featured_image'));
                                $recordUrl = $featuredRecord instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $featuredRecord->slug) : null;
                                $recordMeta = $newsMetaFor($featuredRecord);
                            @endphp

                            <article wire:key="news-stacked-featured-{{ $featuredRecord->getKey() ?? $featuredRecord->slug ?? $recordTitle }}" class="grid gap-8 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] lg:items-center">
                                <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                    @if ($recordImage)
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[16/8] w-full object-cover" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[16/8] w-full object-cover" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[16/8] w-full" icon="newspaper" />
                                    @endif
                                </div>
                                <div>
                                    <h3 class="cx-public-featured-title tracking-tight text-zinc-950 dark:text-white">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                        @else
                                            {{ $recordTitle }}
                                        @endif
                                    </h3>
                                    @if (! empty($recordMeta))
                                        <p class="mt-3 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                    @endif
                                    @if ($recordDescription)
                                        <p class="mt-5 cx-public-body text-zinc-700 dark:text-zinc-300">{{ $recordDescription }}</p>
                                    @endif
                                    @if ($recordUrl)
                                        <a href="{{ $recordUrl }}" class="mt-6 inline-flex cursor-pointer items-center gap-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                            {{ $newsReadMoreLabel }}
                                            <flux:icon name="arrow-right" class="size-4" />
                                        </a>
                                    @endif
                                </div>
                            </article>
                        @endif

                        @if ($lowerRecords->isNotEmpty())
                            <div class="cx-public-section-content-compact grid gap-8 lg:grid-cols-2">
                                @foreach ($lowerRecords as $record)
                                    @php
                                        $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                        $recordDescriptionSource = method_exists($record, 'localized')
                                            ? ($record->localized('excerpt') ?: $record->localized('content'))
                                            : data_get($record, 'description');
                                        $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(120)->toString() : null;
                                        $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                        $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                        $recordMeta = $newsMetaFor($record);
                                    @endphp
                                    <article wire:key="news-stacked-lower-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="cx-public-grid cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20 lg:grid-cols-[minmax(12rem,0.9fr)_minmax(0,1fr)] lg:items-start">
                                        <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                            @if ($recordImage)
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                    </a>
                                                @else
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                @endif
                                            @else
                                                <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" icon="newspaper" />
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                                @if ($recordUrl)
                                                    <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                                @else
                                                    {{ $recordTitle }}
                                                    @endif
                                                </h3>
                                                @if (! empty($recordMeta))
                                                    <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                                @endif
                                                @if ($recordDescription)
                                                <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                            @endif
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="mt-4 inline-flex cursor-pointer items-center gap-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                    {{ $newsReadMoreLabel }}
                                                    <flux:icon name="arrow-right" class="size-4" />
                                                </a>
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif

                        @if ($remainingRecords->isNotEmpty())
                            <div class="cx-public-section-content-compact cx-public-grid-loose sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($remainingRecords as $record)
                                    @php
                                        $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                        $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                        $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(110)->toString() : null;
                                        $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                        $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                        $recordMeta = $newsMetaFor($record);
                                    @endphp
                                    <article wire:key="news-stacked-more-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}">
                                        @if ($recordImage)
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                    <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                                </a>
                                            @else
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                            @endif
                                        @endif
                                        <h3 class="mt-4 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if (! empty($recordMeta))
                                            <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                        @endif
                                        @if ($recordDescription)
                                            <p class="mt-2 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'editorial_list')
                    <div class="cx-public-section-content mx-auto max-w-4xl cx-public-stack-showcase">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(180)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                $recordAuthor = $showNewsAuthor ? $newsAuthorFor($record) : null;
                                $recordAuthorInitial = $recordAuthor ? mb_strtoupper(mb_substr($recordAuthor, 0, 1, 'UTF-8'), 'UTF-8') : null;
                                $recordPublishedDate = $showNewsDate ? $newsPublishedDateFor($record) : null;
                                $recordPublishedAt = data_get($record, 'published_at');
                                $recordPublishedDateMachine = $recordPublishedAt
                                    ? ($recordPublishedAt instanceof \Illuminate\Support\Carbon ? $recordPublishedAt : \Illuminate\Support\Carbon::parse($recordPublishedAt))->toDateString()
                                    : null;
                            @endphp

                            <article wire:key="news-editorial-list-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="group grid gap-7 lg:grid-cols-[16rem_minmax(0,1fr)] lg:items-start">
                                <figure class="cx-public-media-frame-surface">
                                    @if ($recordImage)
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-video w-full object-cover cx-public-image-zoom sm:aspect-[2/1] lg:aspect-square" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-video w-full object-cover sm:aspect-[2/1] lg:aspect-square" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-video w-full sm:aspect-[2/1] lg:aspect-square" icon="newspaper" icon-class="size-9" />
                                    @endif
                                </figure>

                                <div>
                                    @if ($recordPublishedDate)
                                        <time class="cx-public-meta text-zinc-500 dark:text-zinc-400" @if ($recordPublishedDateMachine) datetime="{{ $recordPublishedDateMachine }}" @endif>
                                            {{ $recordPublishedDate }}
                                        </time>
                                    @endif

                                    <div class="relative max-w-xl">
                                        <h3 class="mt-3 cx-public-item-title text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                    {{ $recordTitle }}
                                                </a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>

                                        @if ($recordDescription)
                                            <p class="mt-4 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </div>

                                    @if ($recordAuthor || $recordUrl)
                                        <div class="mt-6 flex flex-wrap items-center justify-between gap-4">
                                            @if ($recordAuthor)
                                                <div class="flex min-w-0 items-center gap-3">
                                                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[color:var(--niva-primary-50)] text-sm font-semibold text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                                        {{ $recordAuthorInitial }}
                                                    </span>
                                                    <p class="min-w-0 truncate cx-public-meta-strong text-zinc-800 dark:text-zinc-200">{{ $recordAuthor }}</p>
                                                </div>
                                            @endif

                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="shrink-0 cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                    {{ $newsReadMoreLabel }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'image_cards')
                    <div class="cx-public-section-content grid auto-rows-fr gap-8 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                $recordAuthor = $showNewsAuthor ? $newsAuthorFor($record) : null;
                                $recordAuthorInitial = $recordAuthor ? mb_strtoupper(mb_substr($recordAuthor, 0, 1, 'UTF-8'), 'UTF-8') : null;
                                $recordPublishedDate = $showNewsDate ? $newsPublishedDateFor($record) : null;
                                $recordPublishedAt = data_get($record, 'published_at');
                                $recordPublishedDateMachine = $recordPublishedAt
                                    ? ($recordPublishedAt instanceof \Illuminate\Support\Carbon ? $recordPublishedAt : \Illuminate\Support\Carbon::parse($recordPublishedAt))->toDateString()
                                    : null;
                            @endphp

                            <article wire:key="news-image-card-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" @class([
                                'group relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden rounded-xl bg-white p-3 shadow-sm shadow-zinc-950/10 ring-1 ring-zinc-200/70 transition duration-200 hover:-translate-y-0.5 hover:shadow-md hover:shadow-zinc-950/10 dark:bg-zinc-950 dark:ring-zinc-800 dark:shadow-black/20',
                                'cursor-pointer' => $recordUrl,
                            ])>
                                @if ($recordImage)
                                    <img src="{{ $recordImage }}" alt="" class="absolute inset-0 -z-20 size-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                @else
                                    <x-corexis::public-image-placeholder class="absolute inset-0 -z-20 size-full" icon="newspaper" icon-class="size-10" />
                                @endif

                                <div class="absolute inset-0 -z-10 bg-gradient-to-t from-white/35 via-white/5 to-transparent dark:from-zinc-950/45 dark:via-zinc-950/10"></div>

                                <div class="relative rounded-lg bg-white/94 p-4 shadow-sm shadow-zinc-950/10 ring-1 ring-white/80 backdrop-blur-sm dark:bg-zinc-950/90 dark:ring-white/10">
                                    @if ($recordPublishedDate || $recordAuthor)
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 overflow-hidden cx-public-meta text-zinc-600 dark:text-zinc-300">
                                            @if ($recordPublishedDate)
                                                <time @if ($recordPublishedDateMachine) datetime="{{ $recordPublishedDateMachine }}" @endif>
                                                    {{ $recordPublishedDate }}
                                                </time>
                                            @endif

                                            @if ($recordPublishedDate && $recordAuthor)
                                                <span class="size-1 rounded-full bg-zinc-300 dark:bg-zinc-600" aria-hidden="true"></span>
                                            @endif

                                            @if ($recordAuthor)
                                                <span class="flex min-w-0 items-center gap-2">
                                                    <span class="grid size-6 shrink-0 place-items-center rounded-full bg-[color:var(--niva-primary-50)] text-[11px] font-semibold text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                                        {{ $recordAuthorInitial }}
                                                    </span>
                                                    <span class="truncate">{{ $recordAuthor }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <h3 class="mt-3 cx-public-item-title text-zinc-950 dark:text-white">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                <span class="absolute inset-0" aria-hidden="true"></span>
                                                {{ $recordTitle }}
                                            </a>
                                        @else
                                            {{ $recordTitle }}
                                        @endif
                                    </h3>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'blog_grid')
                    <div class="cx-public-section-content grid gap-x-8 gap-y-14 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(135)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                $recordAuthor = $showNewsAuthor ? $newsAuthorFor($record) : null;
                                $recordAuthorInitial = $recordAuthor ? mb_strtoupper(mb_substr($recordAuthor, 0, 1, 'UTF-8'), 'UTF-8') : null;
                                $recordPublishedDate = $showNewsDate ? $newsPublishedDateFor($record) : null;
                                $recordPublishedAt = data_get($record, 'published_at');
                                $recordPublishedDateMachine = $recordPublishedAt
                                    ? ($recordPublishedAt instanceof \Illuminate\Support\Carbon ? $recordPublishedAt : \Illuminate\Support\Carbon::parse($recordPublishedAt))->toDateString()
                                    : null;
                            @endphp

                            <article wire:key="news-blog-grid-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="group flex h-full flex-col items-start">
                                <figure class="relative w-full cx-public-media-frame-surface">
                                    @if ($recordImage)
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[16/10] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[16/10] w-full object-cover" loading="lazy" decoding="async">
                                        @endif
                                    @else
                                        <x-corexis::public-image-placeholder class="aspect-[16/10] w-full" icon="newspaper" icon-class="size-9" />
                                    @endif
                                </figure>

                                <div class="flex max-w-xl grow flex-col">
                                    @if ($recordPublishedDate)
                                        <time class="mt-6 cx-public-meta text-zinc-500 dark:text-zinc-400" @if ($recordPublishedDateMachine) datetime="{{ $recordPublishedDateMachine }}" @endif>
                                            {{ $recordPublishedDate }}
                                        </time>
                                    @endif

                                    <div class="relative grow">
                                        <h3 class="mt-3 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                    {{ $recordTitle }}
                                                </a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>

                                        @if ($recordDescription)
                                            <p class="mt-4 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </div>

                                    <div class="mt-6 flex items-center justify-between gap-4">
                                        @if ($recordAuthor)
                                            <div class="flex min-w-0 items-center gap-3">
                                                <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[color:var(--niva-primary-50)] text-sm font-semibold text-[color:var(--niva-primary-700)] ring-1 ring-[color:var(--niva-primary-100)] dark:bg-[color:var(--niva-primary-950)] dark:text-[color:var(--niva-primary-300)] dark:ring-[color:var(--niva-primary-900)]">
                                                    {{ $recordAuthorInitial }}
                                                </span>
                                                <p class="min-w-0 truncate cx-public-meta-strong text-zinc-800 dark:text-zinc-200">{{ $recordAuthor }}</p>
                                            </div>
                                        @endif

                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="shrink-0 cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">
                                                {{ $newsReadMoreLabel }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @elseif ($isNewsSection && $newsLayout === 'journal')
                    <div class="cx-public-section-content cx-public-stack-loose">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(160)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = $record instanceof \IvanBaric\Blog\Models\Post ? $this->postContentUrl((string) $record->slug) : null;
                                $recordMeta = $newsMetaFor($record);
                            @endphp
                            <article wire:key="news-journal-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="group cx-public-grid cx-public-surface-plain cx-public-card-padding-compact cx-public-card-hover dark:bg-zinc-950 dark:shadow-black/20 md:grid-cols-[14rem_1fr] md:items-center">
                                @if ($recordImage)
                                    <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                                <img src="{{ $recordImage }}" alt="" class="aspect-[16/10] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                            </a>
                                        @else
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[16/10] w-full object-cover cx-public-image-zoom" loading="lazy" decoding="async">
                                        @endif
                                    </div>
                                @else
                                    <div class="overflow-hidden rounded-xl bg-zinc-100 dark:bg-zinc-900">
                                        <x-corexis::public-image-placeholder class="aspect-[16/10] w-full" :icon="$isNewsSection ? 'newspaper' : 'cube'" />
                                    </div>
                                @endif
                                <div>
                                    <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                        @if ($recordUrl)
                                            <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                        @else
                                            {{ $recordTitle }}
                                        @endif
                                    </h3>
                                    @if (! empty($recordMeta))
                                        <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                    @endif
                                    @if ($recordDescription)
                                        <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                    @endif
                                    @if ($recordUrl)
                                        <a href="{{ $recordUrl }}" class="mt-4 inline-flex cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $newsReadMoreLabel }}</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="cx-public-section-content cx-public-grid md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($displayRecords as $record)
                            @php
                                $recordTitle = method_exists($record, 'localized') ? $record->localized('title') : data_get($record, 'title');
                                $recordDescriptionSource = method_exists($record, 'localized') ? ($record->localized('excerpt') ?: $record->localized('content')) : data_get($record, 'description');
                                $recordDescription = filled($recordDescriptionSource) ? str((string) $recordDescriptionSource)->stripTags()->squish()->limit(140)->toString() : null;
                                $recordImage = method_exists($record, 'featuredImageUrl') ? $record->featuredImageUrl() : $assetUrl(data_get($record, 'image') ?: data_get($record, 'featured_image'));
                                $recordUrl = match (true) {
                                    $record instanceof \IvanBaric\Blog\Models\Post => $this->postContentUrl((string) $record->slug),
                                    \IvanBaric\NivaTemplate\Support\NivaTemplateModels::isProduct($record) => $this->productContentUrl((string) ($record->slug ?: $record->uuid)),
                                    is_array($record) => data_get($record, 'url'),
                                    default => null,
                                };
                                $recordPrice = $productPriceFor($record);
                                $recordMeta = $isNewsSection ? $newsMetaFor($record) : [];
                            @endphp
                            <article wire:key="news-card-{{ $record->getKey() ?? $record->slug ?? $recordTitle }}" class="flex h-full flex-col overflow-hidden rounded-xl bg-zinc-50 shadow-sm shadow-zinc-950/5 cx-public-card-hover hover:bg-white dark:bg-zinc-900 dark:shadow-black/20 dark:hover:bg-zinc-900/80">
                                @if ($recordImage)
                                    @if ($recordUrl)
                                        <a href="{{ $recordUrl }}" class="block cursor-pointer" title="{{ $recordTitle }}" aria-label="{{ $recordTitle }}">
                                            <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                        </a>
                                    @else
                                        <img src="{{ $recordImage }}" alt="" class="aspect-[4/3] w-full object-cover" loading="lazy" decoding="async">
                                    @endif
                                @else
                                    <x-corexis::public-image-placeholder class="aspect-[4/3] w-full" :icon="$isNewsSection ? 'newspaper' : 'cube'" />
                                @endif
                                <div class="flex flex-1 flex-col p-6">
                                    <div class="flex-1">
                                        <h3 class="cx-public-item-title tracking-tight text-zinc-950 dark:text-white">
                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="cursor-pointer transition hover:text-[color:var(--niva-primary-800)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $recordTitle }}</a>
                                            @else
                                                {{ $recordTitle }}
                                            @endif
                                        </h3>
                                        @if (! empty($recordMeta))
                                            <p class="mt-2 cx-public-meta-strong text-[color:var(--niva-primary-700)] dark:text-[color:var(--niva-primary-300)]">{{ implode(' · ', $recordMeta) }}</p>
                                        @endif
                                        @if ($recordDescription)
                                            <p class="mt-3 cx-public-item-text text-zinc-600 dark:text-zinc-300">{{ $recordDescription }}</p>
                                        @endif
                                    </div>

                                    @if ($recordPrice)
                                        <div class="mt-6 border-t cx-public-divider pt-4">
                                            <p class="cx-public-small-strong uppercase tracking-[0.14em] text-zinc-400 dark:text-zinc-500">{{ __('Cijena') }}</p>
                                            <p class="mt-1 cx-public-item-title tracking-tight text-zinc-950 dark:text-white">{{ $recordPrice }}</p>
                                        </div>
                                    @endif

                                    @if ($recordUrl)
                                        <a href="{{ $recordUrl }}" class="mt-5 inline-flex cursor-pointer cx-public-meta-strong text-[color:var(--niva-primary-700)] transition hover:text-[color:var(--niva-primary-800)] dark:text-[color:var(--niva-primary-300)] dark:hover:text-[color:var(--niva-primary-200)]">{{ $newsReadMoreLabel }}</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                    @if (! $hideLoadMoreForProductCarousel && $this->hasMoreRecords())
                    <div class="cx-public-section-content flex justify-center">
                        <button
                            type="button"
                            wire:click="loadMore"
                            wire:loading.attr="disabled"
                            wire:target="loadMore"
                            class="group cx-public-button-primary"
                        >
                            <span wire:loading.remove wire:target="loadMore">{{ $loadMoreLabel }}</span>
                            <span wire:loading wire:target="loadMore">{{ __('Učitavam...') }}</span>
                            <flux:icon name="arrow-down" class="size-4 transition duration-200 group-hover:translate-y-0.5" wire:loading.remove wire:target="loadMore" />
                            <flux:icon.loading class="size-4" wire:loading wire:target="loadMore" />
                        </button>
                    </div>
                    @endif
                @endif
            </div>
        <?php } ?>


