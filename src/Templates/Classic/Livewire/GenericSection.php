<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Templates\Classic\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use IvanBaric\Blog\Models\Post;
use IvanBaric\Corexis\Support\PublicEmptyStatePreview;
use IvanBaric\Gallery\Models\Gallery;
use IvanBaric\NivaTemplate\Concerns\InteractsWithPublicSectionCache;
use IvanBaric\NivaTemplate\Support\NivaTemplateModels;
use IvanBaric\NivaTemplate\Support\SocialLinks;
use IvanBaric\Pages\Livewire\Concerns\CyclesSectionLayoutVariants;
use IvanBaric\Pages\Livewire\Concerns\CyclesSingletonLayoutVariants;
use IvanBaric\Pages\Models\Page;
use IvanBaric\Pages\Models\Section;
use IvanBaric\Pages\Models\SectionItem;
use IvanBaric\Pages\Support\OnePageNavigation;
use IvanBaric\Pages\Support\PagesModels;
use IvanBaric\Pages\Support\PublicSiteUrl;
use IvanBaric\TemplateEngine\Livewire\BaseTemplateSectionComponent;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

/**
 * @phpstan-type ProductCarouselRecord array{id: mixed, type: 'product', title: string, description: string, image: string|null, image_large: string|null, url: string|null, price: float|null, count: null, count_label: null}
 * @phpstan-type GalleryCarouselRecord array{id: mixed, type: 'gallery', title: string, description: string|null, image: string|null, image_large: string|null, url: string|null, price: null, count: int, count_label: string}
 * @phpstan-type CarouselSectionItem array{id: mixed, type: 'section_item', title: string, subtitle: string, description: string, content: string, image: string|null}
 */
final class GenericSection extends BaseTemplateSectionComponent
{
    use CyclesSectionLayoutVariants;
    use CyclesSingletonLayoutVariants;
    use InteractsWithPublicSectionCache;

    private const CAROUSEL_INITIAL_LIMIT = 6;

    private const CAROUSEL_LOAD_STEP = 6;

    private const ORGANIZATIONS_REQUEST_CACHE = 'niva.generic-section.organizations';

    private const PAGE_SLUGS_REQUEST_CACHE = 'niva.generic-section.page-slugs';

    private const TEMPLATE_SETTINGS_REQUEST_CACHE = 'niva.generic-section.template-settings';

    private const PREVIEW_EMPTY_STATE_TYPES = [
        'statistics',
        'features',
        'featured_values',
        'content_blocks',
        'collaboration',
        'partners',
        'gallery',
        'gallery_grid',
        'photo_gallery',
        'faq',
        'testimonials',
        'featured_products',
        'all_products',
        'featured_news',
        'latest_news',
        'taxonomy_news',
        'how_to_order',
        'mission',
        'vision',
        'values',
        'team',
        'contact',
        'social_links',
        'video',
    ];

    #[Url(as: 'kategorija', except: '')]
    public string $categoryFilter = '';

    #[Url(as: 'oznaka', except: '')]
    public string $tagFilter = '';

    #[Locked]
    public ?int $visibleRecordsLimit = null;

    /** @var array<string, int> */
    #[Locked]
    public array $visibleCarouselLimits = [];

    #[Locked]
    public bool $emptyStatePreview = false;

    #[Locked]
    public bool $removedFromPublicPage = false;

    /** @param array<string, mixed> $data */
    public function mount(mixed $page = null, mixed $section = null, string $templateKey = '', array $data = []): void
    {
        parent::mount($page, $section, $templateKey, $data);

        $this->emptyStatePreview = app(PublicEmptyStatePreview::class)->enabledForTeam($this->resolvedTeamId());

        $type = (string) data_get($this->section, 'type');

        if ($this->supportsLoadMore($type) && ! $this->usesRecordsCarousel($type)) {
            $this->visibleRecordsLimit = $this->baseRecordsLimit($type);
        }
    }

    /**
     * @return Collection<int, covariant ProductCarouselRecord>|Collection<int, covariant GalleryCarouselRecord>|EloquentCollection<int, Model>|EloquentCollection<int, Post>|EloquentCollection<int, Gallery>
     */
    public function records(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        $type = (string) data_get($this->section, 'type');
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (in_array($type, ['featured_products', 'all_products'], true)) {
            if ($this->usesRecordsCarousel($type)) {
                return $this->carouselRecords($type);
            }

            $productModel = NivaTemplateModels::product();

            if ($productModel === null) {
                return collect();
            }

            $limit = $this->recordsLimit($type);

            return $this->rememberSectionModels(
                'records.products',
                $productModel,
                $this->recordsCacheContext($type, $limit),
                true,
                fn (): EloquentCollection => $this->productRecordsQuery($type)->limit($limit)->get(),
            );
        }

        if (in_array($type, ['featured_news', 'latest_news', 'taxonomy_news'], true)) {
            if (! is_numeric($teamId)) {
                return collect();
            }

            $postModel = $this->postModel();
            $postInstance = new $postModel;
            $limit = $this->recordsLimit($type);
            $query = $postModel::query()
                ->forTenant((int) $teamId)
                ->published()
                ->when($this->newsContentSource($type) === 'featured', fn ($query) => $query->featured())
                ->with(['author', 'galleries.featuredMedia', 'galleries.firstMedia']);

            if ($this->usesPostFilters()) {
                $postTable = $postInstance->getTable();
                $postMorphClass = $postInstance->getMorphClass();

                $this->applyPostTaxonomyFilter(
                    query: $query,
                    teamId: (int) $teamId,
                    types: ['category', 'post_category'],
                    slug: $this->activeCategoryFilter(),
                    postTable: $postTable,
                    postMorphClass: $postMorphClass,
                );

                $this->applyPostTaxonomyFilter(
                    query: $query,
                    teamId: (int) $teamId,
                    types: ['tags'],
                    slug: $this->activeTagFilter(),
                    postTable: $postTable,
                    postMorphClass: $postMorphClass,
                );
            }

            if ($this->newsContentSource($type) === 'taxonomy') {
                $this->applyConfiguredPostTaxonomyFilter(
                    query: $query,
                    teamId: (int) $teamId,
                    postTable: $postInstance->getTable(),
                    postMorphClass: $postInstance->getMorphClass(),
                );
            }

            return $this->rememberSectionModels(
                'records.posts',
                $postModel,
                $this->recordsCacheContext($type, $limit),
                true,
                fn (): EloquentCollection => $query->ordered()->limit($limit)->get(),
            );
        }

        if (in_array($type, ['gallery', 'gallery_grid'], true)) {
            if ($this->usesDirectGallery()) {
                return collect();
            }

            if ($this->usesRecordsCarousel($type)) {
                return $this->carouselRecords($type);
            }

            $limit = $this->recordsLimit($type);

            return $this->rememberSectionModels(
                'records.galleries',
                Gallery::class,
                $this->recordsCacheContext($type, $limit),
                true,
                fn (): EloquentCollection => $this->galleryRecordsQuery()->limit($limit)->get(),
            );
        }

        return collect();
    }

    /** @return Collection<int, covariant CarouselSectionItem>|EloquentCollection<int, SectionItem> */
    public function sectionItems(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        $type = (string) data_get($this->section, 'type');

        if ($type === 'testimonials' && (string) data_get($this->section, 'settings.layout_variant') === 'carousel') {
            return $this->carouselSectionItems('testimonials');
        }

        return $this->cachedSectionItems();
    }

    public function emptyStatePreviewEnabled(): bool
    {
        return $this->emptyStatePreview;
    }

    /**
     * @param  Collection<int, mixed>  $items
     * @param  Collection<int, mixed>  $records
     */
    public function shouldShowPreviewEmptyState(string $type, Collection $items, Collection $records): bool
    {
        if (in_array($type, ['gallery', 'gallery_grid'], true)) {
            return false;
        }

        return $this->emptyStatePreviewEnabled()
            && in_array($type, self::PREVIEW_EMPTY_STATE_TYPES, true)
            && $items->isEmpty()
            && $records->isEmpty();
    }

    public function previewEmptyStateMessage(string $type): string
    {
        return match ($type) {
            'featured_products', 'all_products' => __('Trenutno nema radova za prikaz.'),
            'featured_news', 'latest_news', 'taxonomy_news' => __('Trenutno nema objava za prikaz.'),
            'gallery', 'gallery_grid', 'photo_gallery' => __('Trenutno nema galerija za prikaz.'),
            default => __('Ova sekcija trenutno nema dodanog sadržaja za prikaz.'),
        };
    }

    /** @return Collection<int, array{name: string, slug: string, count: int, active: bool, url: string}> */
    public function postCategoryFilters(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        return $this->postTaxonomyFilters(['category', 'post_category'], 'kategorija', $this->activeCategoryFilter());
    }

    public function hasActivePostFilter(): bool
    {
        if ($this->emptyStatePreviewEnabled()) {
            return false;
        }

        if (! $this->usesPostFilters()) {
            return false;
        }

        return $this->activeCategoryFilter() !== '' || $this->activeTagFilter() !== '';
    }

    /** @return Collection<int, array{name: string, label: string, query_key: string, clear_url: string}> */
    public function activePostFilters(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        if (! $this->usesPostFilters()) {
            return collect();
        }

        return collect([
            $this->activePostTaxonomyFilter(['category', 'post_category'], $this->activeCategoryFilter(), 'kategorija', __('Kategorija')),
            $this->activePostTaxonomyFilter(['tags'], $this->activeTagFilter(), 'oznaka', __('Oznaka')),
        ])->filter()->values();
    }

    public function usesPostFilters(): bool
    {
        $type = (string) data_get($this->section, 'type');

        if ($type !== 'latest_news' || $this->newsContentSource($type) !== 'all') {
            return false;
        }

        $configured = data_get($this->section, 'settings.is_filterable');

        if ($configured !== null) {
            return filter_var($configured, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        }

        $pageKey = Str::slug((string) data_get($this->page, 'page_key', ''));
        $pageSlug = Str::slug((string) data_get($this->page, 'slug', ''));

        return in_array($pageKey, ['posts', 'blog'], true)
            || in_array($pageSlug, ['posts', 'objave', 'blog'], true);
    }

    public function selectPostCategoryFilter(string $slug): void
    {
        $this->categoryFilter = $this->normalizedPostFilter($slug);
    }

    public function clearPostCategoryFilter(): void
    {
        $this->categoryFilter = '';
    }

    public function clearPostTagFilter(): void
    {
        $this->tagFilter = '';
    }

    public function clearPostFilters(): void
    {
        $this->categoryFilter = '';
        $this->tagFilter = '';
    }

    public function postFilterUrl(string $queryKey, string $slug): string
    {
        if (! in_array($queryKey, ['kategorija', 'oznaka'], true)) {
            return $this->postFilterResetUrl();
        }

        $query = $this->currentPostFilterQuery();
        $query[$queryKey] = $this->normalizedPostFilter($slug);
        unset($query['page']);

        return $this->postFilterUrlForQuery($query);
    }

    public function postFilterClearUrl(string $queryKey): string
    {
        if (! in_array($queryKey, ['kategorija', 'oznaka'], true)) {
            return $this->postFilterResetUrl();
        }

        $query = $this->currentPostFilterQuery();
        unset($query[$queryKey], $query['page']);

        return $this->postFilterUrlForQuery($query);
    }

    public function postFilterResetUrl(): string
    {
        $query = $this->currentPostFilterQuery();
        unset($query['kategorija'], $query['oznaka'], $query['page']);

        return $this->postFilterUrlForQuery($query);
    }

    /**
     * @param  array<int, string>  $types
     * @return array{name: string, label: string, query_key: string, clear_url: string}|null
     */
    private function activePostTaxonomyFilter(array $types, string $slug, string $queryKey, string $label): ?array
    {
        $slug = $this->normalizedPostFilter($slug);

        if ($slug === '') {
            return null;
        }

        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $item = $this->rememberSectionValue(
            'taxonomy.active-filter',
            [
                'types' => $types,
                'slug' => $slug,
            ],
            false,
            function () use ($teamId, $types, $slug): ?array {
                $item = DB::table('taxonomy_items')
                    ->join('taxonomies', 'taxonomy_items.taxonomy_id', '=', 'taxonomies.id')
                    ->where('taxonomy_items.team_id', (int) $teamId)
                    ->where('taxonomies.team_id', (int) $teamId)
                    ->whereIn('taxonomies.type', $types)
                    ->where('taxonomy_items.slug', $slug)
                    ->first(['taxonomy_items.name']);

                return $item ? ['name' => (string) $item->name] : null;
            },
        );

        return [
            'name' => (string) (data_get($item, 'name') ?: str_replace('-', ' ', $slug)),
            'label' => $label,
            'query_key' => $queryKey,
            'clear_url' => $this->postFilterClearUrl($queryKey),
        ];
    }

    /**
     * @param  array<int, string>  $types
     * @return Collection<int, array{name: string, slug: string, count: int, active: bool, url: string}>
     */
    private function postTaxonomyFilters(array $types, string $queryKey, string $activeSlug): Collection
    {
        $type = (string) data_get($this->section, 'type');

        if (! $this->usesPostFilters()) {
            return collect();
        }

        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return collect();
        }

        $teamId = (int) $teamId;
        $postModel = $this->postModel();
        $postInstance = new $postModel;
        $postTable = $postInstance->getTable();
        $postMorphClass = $postInstance->getMorphClass();

        $items = $this->rememberSectionValue(
            'taxonomy.filters',
            [
                'types' => $types,
                'post_table' => $postTable,
                'post_morph_class' => $postMorphClass,
                'time_bucket' => now()->format('YmdH'),
            ],
            true,
            fn (): array => $this->postTaxonomyFilterRows($teamId, $types, $postTable, $postMorphClass),
        );

        return collect($items)
            ->map(fn (array $item): array => [
                'name' => $item['name'],
                'slug' => $item['slug'],
                'count' => $item['count'],
                'active' => $activeSlug === $item['slug'],
                'url' => $this->postFilterUrl($queryKey, $item['slug']),
            ]);
    }

    /**
     * @param  array<int, string>  $types
     * @return array<int, array{name: string, slug: string, count: int}>
     */
    private function postTaxonomyFilterRows(int $teamId, array $types, string $postTable, string $postMorphClass): array
    {
        return DB::table('taxonomy_items')
            ->join('taxonomies', 'taxonomy_items.taxonomy_id', '=', 'taxonomies.id')
            ->join('taxonomyables', 'taxonomy_items.id', '=', 'taxonomyables.taxonomy_item_id')
            ->join($postTable, 'taxonomyables.taxonomyable_id', '=', $postTable.'.id')
            ->where('taxonomy_items.team_id', $teamId)
            ->where('taxonomies.team_id', $teamId)
            ->whereIn('taxonomies.type', $types)
            ->where('taxonomyables.team_id', $teamId)
            ->where('taxonomyables.taxonomyable_type', $postMorphClass)
            ->where($postTable.'.team_id', $teamId)
            ->where($postTable.'.status', 'published')
            ->whereNotNull($postTable.'.published_at')
            ->where($postTable.'.published_at', '<=', now())
            ->when(Schema::hasColumn($postTable, 'deleted_at'), fn ($query) => $query->whereNull($postTable.'.deleted_at'))
            ->groupBy('taxonomy_items.id', 'taxonomy_items.name', 'taxonomy_items.slug', 'taxonomy_items.position')
            ->orderBy('taxonomy_items.position')
            ->orderBy('taxonomy_items.name')
            ->get([
                'taxonomy_items.name',
                'taxonomy_items.slug',
                DB::raw('COUNT(DISTINCT taxonomyables.taxonomyable_id) as posts_count'),
            ])
            ->map(fn (object $item): array => [
                'name' => (string) $item->name,
                'slug' => (string) $item->slug,
                'count' => (int) $item->posts_count,
            ])
            ->all();
    }

    /**
     * @param  Builder<Post>  $query
     * @param  array<int, string>  $types
     */
    private function applyPostTaxonomyFilter(Builder $query, int $teamId, array $types, string $slug, string $postTable, string $postMorphClass): void
    {
        $slug = $this->normalizedPostFilter($slug);

        if ($slug === '') {
            return;
        }

        $query->whereExists(function ($query) use ($teamId, $types, $slug, $postTable, $postMorphClass): void {
            $query->selectRaw('1')
                ->from('taxonomyables')
                ->join('taxonomy_items', 'taxonomyables.taxonomy_item_id', '=', 'taxonomy_items.id')
                ->join('taxonomies', 'taxonomy_items.taxonomy_id', '=', 'taxonomies.id')
                ->whereColumn('taxonomyables.taxonomyable_id', $postTable.'.id')
                ->where('taxonomyables.taxonomyable_type', $postMorphClass)
                ->where('taxonomyables.team_id', $teamId)
                ->where('taxonomy_items.team_id', $teamId)
                ->where('taxonomy_items.slug', $slug)
                ->where('taxonomies.team_id', $teamId)
                ->whereIn('taxonomies.type', $types);
        });
    }

    /** @param Builder<Post> $query */
    private function applyConfiguredPostTaxonomyFilter(Builder $query, int $teamId, string $postTable, string $postMorphClass): void
    {
        $taxonomyItemUuids = $this->configuredTaxonomyItemUuids();

        if ($taxonomyItemUuids === []) {
            return;
        }

        $query->whereExists(function ($query) use ($teamId, $taxonomyItemUuids, $postTable, $postMorphClass): void {
            $query->selectRaw('1')
                ->from('taxonomyables')
                ->join('taxonomy_items', 'taxonomyables.taxonomy_item_id', '=', 'taxonomy_items.id')
                ->join('taxonomies', 'taxonomy_items.taxonomy_id', '=', 'taxonomies.id')
                ->whereColumn('taxonomyables.taxonomyable_id', $postTable.'.id')
                ->where('taxonomyables.taxonomyable_type', $postMorphClass)
                ->where('taxonomyables.team_id', $teamId)
                ->where('taxonomy_items.team_id', $teamId)
                ->whereIn('taxonomy_items.uuid', $taxonomyItemUuids)
                ->where('taxonomies.team_id', $teamId)
                ->whereIn('taxonomies.type', ['category', 'post_category', 'tags']);
        });
    }

    /** @return array<int, string> */
    private function configuredTaxonomyItemUuids(): array
    {
        return collect((array) data_get($this->section, 'settings.taxonomy_item_uuids', []))
            ->filter(static fn (mixed $uuid): bool => is_string($uuid) && Str::isUuid($uuid))
            ->unique()
            ->take(100)
            ->values()
            ->all();
    }

    public function usesDirectGallery(): bool
    {
        $type = (string) data_get($this->section, 'type');

        return in_array($type, ['gallery', 'gallery_grid'], true)
            && in_array($this->galleryContentSource($type), ['direct', 'selected_gallery'], true);
    }

    public function activeGalleryMedia(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        $type = (string) data_get($this->section, 'type');

        if (in_array($type, ['gallery', 'gallery_grid'], true) && $this->galleryContentSource($type) === 'selected_gallery') {
            $gallery = $this->selectedGallery();

            return $gallery?->getMedia($gallery->collection_name)->values() ?? collect();
        }

        if (method_exists($this->section, 'galleryMedia')) {
            return $this->section->galleryMedia((string) config('gallery.default_collection', 'images'))->values();
        }

        return collect();
    }

    private function activeCategoryFilter(): string
    {
        return $this->normalizedPostFilter($this->categoryFilter ?: $this->requestQueryString('kategorija'));
    }

    private function activeTagFilter(): string
    {
        return $this->normalizedPostFilter($this->tagFilter ?: $this->requestQueryString('oznaka'));
    }

    private function normalizedPostFilter(string $value): string
    {
        $value = trim($value);

        return $value === '' ? '' : Str::slug($value);
    }

    private function requestQueryString(string $key): string
    {
        $value = request()->query($key, '');

        return is_string($value) ? $value : '';
    }

    /**
     * @return array<string, mixed>
     */
    private function currentPostFilterQuery(): array
    {
        $query = request()->query();

        unset($query['kategorija'], $query['oznaka'], $query['page']);

        $category = $this->activeCategoryFilter();
        $tag = $this->activeTagFilter();

        if ($category !== '') {
            $query['kategorija'] = $category;
        }

        if ($tag !== '') {
            $query['oznaka'] = $tag;
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $query
     */
    private function postFilterUrlForQuery(array $query): string
    {
        $baseUrl = $this->currentPublicPageUrl();

        return $query === []
            ? $baseUrl
            : $baseUrl.'?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986);
    }

    private function currentPublicPageUrl(): string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (is_numeric($teamId) && $this->page instanceof Page) {
            $organization = $this->templateSectionOrganization((int) $teamId);

            if ($organization) {
                return app(PublicSiteUrl::class)->page($organization, $this->page)
                    ?? url()->current();
            }
        }

        return url()->current();
    }

    public function loadMore(): void
    {
        if ($this->emptyStatePreviewEnabled()) {
            return;
        }

        $type = (string) data_get($this->section, 'type');

        if (! $this->supportsLoadMore($type) || $this->usesRecordsCarousel($type)) {
            return;
        }

        $this->visibleRecordsLimit = min(
            $this->totalRecords($type),
            ($this->visibleRecordsLimit ?: $this->baseRecordsLimit($type)) + $this->loadMoreStep($type),
        );
    }

    public function loadMoreCarousel(string $key): void
    {
        if ($this->emptyStatePreviewEnabled()) {
            return;
        }

        if (! $this->usesLoadableCarousel($key)) {
            return;
        }

        $this->visibleCarouselLimits[$key] = min(
            $this->totalCarouselItems($key),
            $this->carouselVisibleLimit($key) + self::CAROUSEL_LOAD_STEP,
        );
    }

    public function hasMoreRecords(): bool
    {
        if ($this->emptyStatePreviewEnabled()) {
            return false;
        }

        $type = (string) data_get($this->section, 'type');

        if (! $this->supportsLoadMore($type) || $this->usesRecordsCarousel($type)) {
            return false;
        }

        return ($this->visibleRecordsLimit ?: $this->baseRecordsLimit($type)) < $this->totalRecords($type);
    }

    public function hasMoreCarouselItems(string $key): bool
    {
        if ($this->emptyStatePreviewEnabled()) {
            return false;
        }

        if (! $this->usesLoadableCarousel($key)) {
            return false;
        }

        return $this->carouselVisibleLimit($key) < $this->totalCarouselItems($key);
    }

    public function contentUrl(string $pageSlug, string $contentSlug): ?string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $organization = $this->templateSectionOrganization((int) $teamId);

        if (! $organization) {
            return null;
        }

        $page = $this->page instanceof Page && (string) $this->page->slug === $pageSlug
            ? $this->page
            : Page::query()->forTenant((int) $teamId)->where('slug', $pageSlug)->first();

        return $page instanceof Page
            ? app(PublicSiteUrl::class)->content($organization, $page, $contentSlug)
            : null;
    }

    public function pageUrlForKey(string $pageKey): ?string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $organizationSlug = $this->organizationSlug((int) $teamId);

        if (! $organizationSlug) {
            return null;
        }

        $pageSlug = $this->contentPageSlug((int) $teamId, $pageKey, match ($pageKey) {
            'contact' => ['contact', 'kontakt'],
            default => [$pageKey],
        });

        if (! $pageSlug) {
            return null;
        }

        return app(PublicSiteUrl::class)->pageForSlug(
            (string) $organizationSlug,
            (string) $pageSlug,
        );
    }

    public function galleryContentUrl(string $contentSlug): ?string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $galleryPageSlug = $this->contentPageSlug((int) $teamId, 'gallery', ['gallery', 'galerija']);

        if (! $galleryPageSlug) {
            return null;
        }

        return $this->contentUrl((string) $galleryPageSlug, $contentSlug);
    }

    public function postContentUrl(string $contentSlug): ?string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $postsPageSlug = $this->contentPageSlug((int) $teamId, 'posts', ['posts', 'objave']);

        if (! $postsPageSlug) {
            return null;
        }

        return $this->contentUrl((string) $postsPageSlug, $contentSlug);
    }

    public function productContentUrl(string $contentSlug): ?string
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (! is_numeric($teamId)) {
            return null;
        }

        $productsPageSlug = $this->contentPageSlug(
            (int) $teamId,
            'products',
            ['products', 'radovi', 'proizvodi', 'radovi-i-rukotvorine'],
        );

        if (! $productsPageSlug) {
            return null;
        }

        return $this->contentUrl((string) $productsPageSlug, $contentSlug);
    }

    /** @return Collection<int, SectionItem> */
    public function calendarEvents(): Collection
    {
        $configuredLimit = data_get($this->section, 'settings.limit');
        $limit = (string) data_get($this->section, 'settings.layout_variant') === 'calendar-carousel'
            ? $this->carouselVisibleLimit('calendar')
            : (is_numeric($configuredLimit) && (int) $configuredLimit > 0 ? (int) $configuredLimit : 6);

        return $this->orderedCalendarEvents()
            ->take($limit)
            ->values();
    }

    public function calendarEventDay(mixed $eventDate): string
    {
        return $this->calendarDate($eventDate)?->format('j') ?? '';
    }

    public function calendarEventMonth(mixed $eventDate): string
    {
        $date = $this->calendarDate($eventDate);

        if (! $date instanceof Carbon) {
            return '';
        }

        return [
            1 => __('sij'),
            2 => __('velj'),
            3 => __('ožu'),
            4 => __('tra'),
            5 => __('svi'),
            6 => __('lip'),
            7 => __('srp'),
            8 => __('kol'),
            9 => __('ruj'),
            10 => __('lis'),
            11 => __('stu'),
            12 => __('pro'),
        ][(int) $date->format('n')];
    }

    public function calendarEventDateLabel(mixed $eventDate): string
    {
        $day = $this->calendarEventDay($eventDate);
        $month = $this->calendarEventMonth($eventDate);

        return $day !== '' && $month !== '' ? $day.'. '.$month : '';
    }

    public function calendarEventTimeRange(mixed $startsAt, mixed $endsAt): string
    {
        $start = $this->normalizedEventTime($startsAt);
        $end = $this->normalizedEventTime($endsAt);

        if ($start === null) {
            return '';
        }

        return $end !== null ? $start.' – '.$end : $start;
    }

    public function sectionAnchorId(): string
    {
        return app(OnePageNavigation::class)->anchorId($this->section);
    }

    public function sectionHeaderVariant(): string
    {
        $variant = (string) data_get($this->templateSectionSettings(), 'section_header_variant', 'left');

        if ($variant === 'center_rule') {
            return 'center';
        }

        return in_array($variant, [
            'left',
            'center',
            'split',
            'side_label',
            'marker',
            'left_accent',
            'center_accent',
            'split_accent',
            'left_colored',
            'center_colored',
            'split_colored',
        ], true) ? $variant : 'left';
    }

    public function canCycleTemplateSectionsVariant(): bool
    {
        $user = auth()->user();
        $sectionTeamId = data_get($this->section, 'team_id');
        $currentTeamId = data_get($user, 'current_team_id');

        return $user !== null
            && is_numeric($sectionTeamId)
            && is_numeric($currentTeamId)
            && (int) $sectionTeamId === (int) $currentTeamId
            && $this->singletonLayoutVariantResolver()->hasCycleableVariants('template_sections');
    }

    public function canManageSection(): bool
    {
        $user = auth()->user();
        $sectionTeamId = data_get($this->section, 'team_id');
        $currentTeamId = data_get($user, 'current_team_id');

        return $user !== null
            && $this->section instanceof Model
            && is_numeric($sectionTeamId)
            && is_numeric($currentTeamId)
            && (int) $sectionTeamId === (int) $currentTeamId
            && corexis_can('pages.sections.manage', $this->section);
    }

    public function cycleTemplateSectionsVariant(string $direction = 'next'): void
    {
        $this->cycleSingletonLayoutVariant('template_sections', $direction);
        $this->dispatch('template-sections-layout-cycled');
    }

    public function nextTemplateSectionsVariantLabel(): string
    {
        return $this->nextSingletonLayoutVariantLabel('template_sections');
    }

    public function previousTemplateSectionsVariantLabel(): string
    {
        return $this->previousSingletonLayoutVariantLabel('template_sections');
    }

    #[On('template-sections-layout-cycled')]
    #[On('pages-public-template-part-updated.sections')]
    public function refreshTemplateSectionSettings(): void
    {
        $this->forgetTemplateSectionSettings();
    }

    #[On('pages-public-section-updated.{section.uuid}')]
    public function refreshEditedSection(): void
    {
        $sectionUuid = (string) data_get($this->section, 'uuid');
        $teamId = $this->resolvedTeamId();
        $pageId = data_get($this->page, 'id') ?? data_get($this->section, 'page_id');

        if ($sectionUuid === '' || $teamId === null || ! is_numeric($pageId)) {
            return;
        }

        $model = PagesModels::section();
        $section = $model::query()
            ->forTenant($teamId)
            ->where('page_id', (int) $pageId)
            ->where('uuid', $sectionUuid)
            ->first();

        if (! $section instanceof Model || ! (bool) data_get($section, 'is_visible', true)) {
            $this->removedFromPublicPage = true;

            return;
        }

        $this->removedFromPublicPage = false;

        $this->sectionLayoutVariantWasCycled($section);
    }

    #[On('pages-public-section-removed.{section.uuid}')]
    public function removeEditedSectionFromPublicPage(): void
    {
        $this->removedFromPublicPage = true;
    }

    #[On('pages-public-template-part-updated.footer')]
    public function refreshFooterBackedSocialLinks(): void
    {
        if ((string) data_get($this->section, 'type') !== 'social_links') {
            return;
        }

        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return;
        }

        $organizations = (array) request()->attributes->get(self::ORGANIZATIONS_REQUEST_CACHE, []);
        unset($organizations[$teamId]);
        request()->attributes->set(self::ORGANIZATIONS_REQUEST_CACHE, $organizations);
    }

    #[On('pages-public-content-source-updated')]
    public function refreshContentSource(string $source): void
    {
        $types = match ($source) {
            'posts' => ['latest_news', 'featured_news', 'taxonomy_news'],
            'products' => ['all_products', 'featured_products'],
            'galleries' => ['gallery', 'gallery_grid'],
            default => [],
        };

        if (! in_array((string) data_get($this->section, 'type'), $types, true)) {
            return;
        }

        if ($this->section instanceof Model) {
            $this->section = $this->section->refresh();
        }
    }

    /** @return array{before: string, accent: string}|null */
    public function sectionTitleAccentParts(?string $title): ?array
    {
        $title = trim((string) $title);

        if ($title === '') {
            return null;
        }

        $words = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $wordCount = count($words);

        if ($wordCount <= 1) {
            return [
                'before' => '',
                'accent' => $title,
            ];
        }

        $accentCount = $wordCount >= 3 ? 2 : 1;

        return [
            'before' => implode(' ', array_slice($words, 0, -$accentCount)),
            'accent' => implode(' ', array_slice($words, -$accentCount)),
        ];
    }

    public function render(): View
    {
        if ($this->removedFromPublicPage) {
            return view('niva-template::templates.classic.removed-section');
        }

        return view('niva-template::templates.classic.generic-section');
    }

    /** @return array<int, array{key: string, label: string, url: string, icon: string}> */
    public function socialLinks(): array
    {
        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return [];
        }

        return app(SocialLinks::class)->fromOrganization($this->templateSectionOrganization($teamId));
    }

    /** @param array<string, mixed> $params */
    public function placeholder(array $params = []): View
    {
        return view('niva-template::templates.classic.section-placeholder', [
            'section' => data_get($params, 'section'),
            'data' => (array) data_get($params, 'data', []),
        ]);
    }

    protected function sectionLayoutVariantWasCycled(Model $section): void
    {
        $section->unsetRelation('galleries');
        $section->unsetRelation('media');

        $this->section = $section;
        $this->data = template_engine()->data($section, $this->templateKey)->merged;
        $this->visibleRecordsLimit = null;
        $this->visibleCarouselLimits = [];
    }

    protected function singletonLayoutVariantModel(string $definitionKey): ?Model
    {
        if ($definitionKey !== 'template_sections') {
            return null;
        }

        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return null;
        }

        return $this->templateSectionOrganization($teamId);
    }

    protected function singletonLayoutVariantWasCycled(Model $model, string $definitionKey): void
    {
        if ($definitionKey === 'template_sections') {
            $this->forgetTemplateSectionSettings();
        }
    }

    private function templateSectionOrganization(int $teamId): ?Model
    {
        /** @var array<int, Model|null> $organizations */
        $organizations = request()->attributes->get(self::ORGANIZATIONS_REQUEST_CACHE, []);

        if (! array_key_exists($teamId, $organizations)) {
            $model = NivaTemplateModels::organization();
            $teamColumn = (string) config('niva-template.organization.team_column', 'team_id');
            $activeColumn = (string) config('niva-template.organization.active_column', 'is_active');
            $activeValue = config('niva-template.organization.active_value', true);

            $organizations[$teamId] = $model === null
                ? null
                : $this->rememberTeamModel(
                    'organization',
                    $model,
                    [
                        'team_column' => $teamColumn,
                        'active_column' => $activeColumn,
                        'active_value' => $activeValue,
                    ],
                    false,
                    fn (): ?Model => $model::query()
                        ->where($teamColumn, $teamId)
                        ->when($activeColumn !== '', fn (Builder $query) => $query->where($activeColumn, $activeValue))
                        ->first(),
                );

            request()->attributes->set(self::ORGANIZATIONS_REQUEST_CACHE, $organizations);
        }

        return $organizations[$teamId];
    }

    private function organizationSlug(int $teamId): ?string
    {
        $slug = $this->templateSectionOrganization($teamId)?->getAttribute('slug');

        return is_string($slug) && $slug !== '' ? $slug : null;
    }

    /** @param array<int, string> $slugs */
    private function contentPageSlug(int $teamId, string $pageKey, array $slugs): ?string
    {
        /** @var array<int, array<string, string|null>> $pageSlugs */
        $pageSlugs = request()->attributes->get(self::PAGE_SLUGS_REQUEST_CACHE, []);

        if (! array_key_exists($pageKey, $pageSlugs[$teamId] ?? [])) {
            $resolved = $this->rememberTeamValue(
                'content-page-slug',
                [
                    'page_key' => $pageKey,
                    'slugs' => $slugs,
                ],
                false,
                fn (): mixed => Page::query()
                    ->forTenant($teamId)
                    ->published()
                    ->where(function (Builder $query) use ($pageKey, $slugs): void {
                        $query->where('page_key', $pageKey)
                            ->orWhereIn('slug', $slugs);
                    })
                    ->value('slug'),
            );

            $pageSlugs[$teamId][$pageKey] = is_string($resolved) && $resolved !== ''
                ? $resolved
                : null;

            request()->attributes->set(self::PAGE_SLUGS_REQUEST_CACHE, $pageSlugs);
        }

        return $pageSlugs[$teamId][$pageKey];
    }

    /** @return array<string, mixed> */
    private function templateSectionSettings(): array
    {
        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $settings */
        $settings = request()->attributes->get(self::TEMPLATE_SETTINGS_REQUEST_CACHE, []);

        if (! array_key_exists($teamId, $settings)) {
            $organization = $this->templateSectionOrganization($teamId);

            $settings[$teamId] = (array) data_get(
                $organization,
                'settings.templates.niva-classic.sections',
                [],
            );

            request()->attributes->set(self::TEMPLATE_SETTINGS_REQUEST_CACHE, $settings);
        }

        return $settings[$teamId];
    }

    private function forgetTemplateSectionSettings(): void
    {
        $teamId = $this->resolvedTeamId();

        if ($teamId !== null) {
            /** @var array<int, array<string, mixed>> $settings */
            $settings = request()->attributes->get(self::TEMPLATE_SETTINGS_REQUEST_CACHE, []);
            /** @var array<int, Model|null> $organizations */
            $organizations = request()->attributes->get(self::ORGANIZATIONS_REQUEST_CACHE, []);

            unset($settings[$teamId], $organizations[$teamId]);

            request()->attributes->set(self::TEMPLATE_SETTINGS_REQUEST_CACHE, $settings);
            request()->attributes->set(self::ORGANIZATIONS_REQUEST_CACHE, $organizations);
        }
    }

    private function baseRecordsLimit(string $type): int
    {
        if (in_array($type, ['featured_products', 'all_products'], true)) {
            $limit = (int) data_get($this->section, 'settings.limit', $type === 'featured_products' ? 6 : 12);

            return in_array($limit, [3, 4, 5, 6, 8, 12], true) ? $limit : ($type === 'featured_products' ? 6 : 12);
        }

        if (in_array($type, ['gallery', 'gallery_grid'], true)) {
            $limit = (int) data_get($this->section, 'settings.limit', 12);

            return in_array($limit, [3, 4, 6, 8, 12], true) ? $limit : 12;
        }

        $defaultLimit = match ($type) {
            'featured_news' => 3,
            'taxonomy_news' => 6,
            default => 12,
        };
        $limit = (int) data_get($this->section, 'settings.limit', $defaultLimit);

        return in_array($limit, [3, 6, 12], true) ? $limit : $defaultLimit;
    }

    private function recordsLimit(string $type): int
    {
        return $this->visibleRecordsLimit ?: $this->baseRecordsLimit($type);
    }

    private function totalRecords(string $type): int
    {
        if ($this->emptyStatePreviewEnabled()) {
            return 0;
        }

        return (int) $this->rememberSectionValue(
            'records.total',
            $this->recordsCacheContext($type),
            true,
            fn (): int => $this->uncachedTotalRecords($type),
        );
    }

    private function uncachedTotalRecords(string $type): int
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        if (in_array($type, ['featured_products', 'all_products'], true)) {
            return (int) $this->productRecordsQuery($type)->count();
        }

        if (in_array($type, ['gallery', 'gallery_grid'], true)) {
            return (int) $this->galleryRecordsQuery()->count();
        }

        if (! is_numeric($teamId)) {
            return 0;
        }

        $postModel = $this->postModel();
        $postInstance = new $postModel;
        $query = $postModel::query()
            ->forTenant((int) $teamId)
            ->published()
            ->when($this->newsContentSource($type) === 'featured', fn ($query) => $query->featured());

        if ($this->newsContentSource($type) === 'taxonomy') {
            $this->applyConfiguredPostTaxonomyFilter(
                query: $query,
                teamId: (int) $teamId,
                postTable: $postInstance->getTable(),
                postMorphClass: $postInstance->getMorphClass(),
            );
        }

        if ($this->usesPostFilters()) {
            $this->applyPostTaxonomyFilter(
                query: $query,
                teamId: (int) $teamId,
                types: ['category', 'post_category'],
                slug: $this->activeCategoryFilter(),
                postTable: $postInstance->getTable(),
                postMorphClass: $postInstance->getMorphClass(),
            );

            $this->applyPostTaxonomyFilter(
                query: $query,
                teamId: (int) $teamId,
                types: ['tags'],
                slug: $this->activeTagFilter(),
                postTable: $postInstance->getTable(),
                postMorphClass: $postInstance->getMorphClass(),
            );
        }

        return (int) $query->count();
    }

    private function loadMoreStep(string $type): int
    {
        return min(6, $this->baseRecordsLimit($type));
    }

    private function carouselVisibleLimit(string $key): int
    {
        $limit = (int) ($this->visibleCarouselLimits[$key] ?? self::CAROUSEL_INITIAL_LIMIT);

        return max(1, $limit);
    }

    /** @return Collection<int, covariant ProductCarouselRecord>|Collection<int, covariant GalleryCarouselRecord> */
    private function carouselRecords(string $key): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        $limit = $this->carouselVisibleLimit($key);

        if (in_array($key, ['featured_products', 'all_products'], true)) {
            $productModel = NivaTemplateModels::product();

            if ($productModel === null) {
                return collect();
            }

            return $this->rememberSectionModels(
                'carousel.products',
                $productModel,
                $this->recordsCacheContext($key, $limit),
                true,
                fn (): EloquentCollection => $this->productRecordsQuery($key)->limit($limit)->get(),
            )
                ->map(fn (Model $product) => $this->productCarouselRecord($product));
        }

        if (in_array($key, ['gallery', 'gallery_grid'], true)) {
            return $this->rememberSectionModels(
                'carousel.galleries',
                Gallery::class,
                $this->recordsCacheContext($key, $limit),
                true,
                fn (): EloquentCollection => $this->galleryRecordsQuery()->limit($limit)->get(),
            )
                ->map(fn (Gallery $gallery) => $this->galleryCarouselRecord($gallery));
        }

        return collect();
    }

    /** @return Collection<int, covariant CarouselSectionItem> */
    private function carouselSectionItems(string $key): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        if ($key !== 'testimonials') {
            return collect();
        }

        return $this->cachedSectionItems()
            ->take($this->carouselVisibleLimit($key))
            ->map(fn (SectionItem $item) => $this->sectionItemCarouselRecord($item));
    }

    /** @return ProductCarouselRecord */
    private function productCarouselRecord(Model $product): array
    {
        $image = method_exists($product, 'featuredImageUrl') ? $product->featuredImageUrl() : null;

        return [
            'id' => $product->getKey(),
            'type' => 'product',
            'title' => method_exists($product, 'localized') ? $product->localized('title') : (string) $product->getAttribute('title'),
            'description' => method_exists($product, 'localized') ? $product->localized('description') : (string) $product->getAttribute('description'),
            'image' => $image,
            'image_large' => $image,
            'url' => $this->productContentUrl((string) ($product->getAttribute('slug') ?: $product->getAttribute('uuid'))),
            'price' => is_numeric($product->getAttribute('price')) ? (float) $product->getAttribute('price') : null,
            'count' => null,
            'count_label' => null,
        ];
    }

    /** @return GalleryCarouselRecord */
    private function galleryCarouselRecord(Gallery $gallery): array
    {
        $cover = $gallery->featuredOrFirstMedia();
        $coverUrl = null;
        $coverLargeUrl = null;

        if ($cover) {
            try {
                $coverUrl = $cover->getAvailableUrl(['thumb', 'large']);
                $coverLargeUrl = $cover->getAvailableUrl(['large', 'thumb']);
            } catch (\Throwable) {
                $coverUrl = $cover->getUrl();
                $coverLargeUrl = $coverUrl;
            }
        }

        $mediaCount = (int) ($gallery->media_count ?? $gallery->getMedia($gallery->collection_name)->count());

        return [
            'id' => $gallery->getKey(),
            'type' => 'gallery',
            'title' => $gallery->displayTitle(),
            'description' => $gallery->description,
            'image' => $coverUrl,
            'image_large' => $coverLargeUrl ?: $coverUrl,
            'url' => $this->galleryContentUrl((string) ($gallery->slug ?: $gallery->uuid)),
            'price' => null,
            'count' => $mediaCount,
            'count_label' => trans_choice(':count fotografija|:count fotografije|:count fotografija', $mediaCount, ['count' => $mediaCount]),
        ];
    }

    /** @return CarouselSectionItem */
    private function sectionItemCarouselRecord(SectionItem $item): array
    {
        return [
            'id' => $item->getKey(),
            'type' => 'section_item',
            'title' => $item->localized('title'),
            'subtitle' => $item->localized('subtitle'),
            'description' => $item->localized('description'),
            'content' => $item->localized('content'),
            'image' => $item->imageUrl(),
        ];
    }

    private function totalCarouselItems(string $key): int
    {
        if ($this->emptyStatePreviewEnabled()) {
            return 0;
        }

        if ($key === 'calendar') {
            return $this->orderedCalendarEvents()->count();
        }

        if ($this->usesSectionItemCarousel($key)) {
            return $this->cachedSectionItems()->count();
        }

        if (in_array($key, ['featured_products', 'all_products', 'gallery', 'gallery_grid'], true)) {
            return $this->totalRecords($key);
        }

        return 0;
    }

    /** @return Builder<Model>|Builder<Page> */
    private function productRecordsQuery(string $type): Builder
    {
        $teamId = $this->resolvedTeamId();
        $model = NivaTemplateModels::product();

        if ($teamId === null || $model === null) {
            return Page::query()->whereRaw('1 = 0');
        }

        $teamColumn = (string) config('niva-template.products.team_column', 'team_id');
        $visibleColumn = (string) config('niva-template.products.visible_column', 'is_visible');
        $featuredColumn = (string) config('niva-template.products.featured_column', 'is_featured');
        $publishedColumn = (string) config('niva-template.products.published_column', 'published_at');
        $orderColumn = (string) config('niva-template.products.order_column', 'sort_order');
        $relationships = array_values(array_filter(
            (array) config('niva-template.products.eager_load', []),
            static fn (mixed $relationship): bool => is_string($relationship) && $relationship !== '',
        ));

        $query = $model::query()
            ->where($teamColumn, $teamId)
            ->where($visibleColumn, true)
            ->where(fn (Builder $query) => $query->whereNull($publishedColumn)->orWhere($publishedColumn, '<=', now()))
            ->when($this->productContentSource($type) === 'featured', fn (Builder $query) => $query->where($featuredColumn, true))
            ->when($relationships !== [], fn (Builder $query) => $query->with($relationships))
            ->orderBy($orderColumn);

        $query->orderByDesc($publishedColumn)
            ->orderByDesc('created_at');

        if ($this->productContentSource($type) === 'taxonomy') {
            $this->applyConfiguredProductTaxonomyFilter($query, $teamId);
        }

        return $query;
    }

    /** @return Builder<Gallery> */
    private function galleryRecordsQuery(): Builder
    {
        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return Gallery::query()->whereRaw('1 = 0');
        }

        $query = Gallery::query()
            ->forTenant($teamId)
            ->standalone()
            ->forCollection((string) config('gallery.default_collection', 'images'))
            ->with(['featuredMedia', 'firstMedia'])
            ->withCount('media')
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at');

        $source = $this->galleryContentSource((string) data_get($this->section, 'type', 'gallery'));
        $selectedGalleryUuid = data_get($this->section, 'settings.gallery_uuid');
        $galleryUuids = collect((array) data_get($this->section, 'settings.gallery_uuids', []))
            ->filter(static fn (mixed $uuid): bool => is_string($uuid) && Str::isUuid($uuid))
            ->unique()
            ->take(100)
            ->values()
            ->all();

        if ($source === 'selected_gallery') {
            $galleryUuid = is_string($selectedGalleryUuid) && Str::isUuid($selectedGalleryUuid)
                ? $selectedGalleryUuid
                : ($galleryUuids[0] ?? null);

            return $query->when(is_string($galleryUuid), fn (Builder $query) => $query->where('uuid', $galleryUuid));
        }

        return $query->when($source === 'albums' && $galleryUuids !== [], fn (Builder $query) => $query->whereIn('uuid', $galleryUuids));
    }

    private function selectedGallery(): ?Gallery
    {
        $teamId = $this->resolvedTeamId();

        if ($teamId === null) {
            return null;
        }

        $selectedGalleryUuid = data_get($this->section, 'settings.gallery_uuid');
        $galleryUuids = collect((array) data_get($this->section, 'settings.gallery_uuids', []))
            ->filter(static fn (mixed $uuid): bool => is_string($uuid) && Str::isUuid($uuid))
            ->unique()
            ->take(1)
            ->values();
        $galleryUuid = is_string($selectedGalleryUuid) && Str::isUuid($selectedGalleryUuid)
            ? $selectedGalleryUuid
            : $galleryUuids->first();

        if (! is_string($galleryUuid)) {
            return null;
        }

        return Gallery::query()
            ->forTenant($teamId)
            ->standalone()
            ->forCollection((string) config('gallery.default_collection', 'images'))
            ->where('uuid', $galleryUuid)
            ->first();
    }

    /** @param Builder<Model> $query */
    private function applyConfiguredProductTaxonomyFilter(Builder $query, int $teamId): void
    {
        $taxonomyItemUuids = $this->configuredTaxonomyItemUuids();

        if ($taxonomyItemUuids === []) {
            return;
        }

        $model = NivaTemplateModels::product();

        if ($model === null) {
            return;
        }

        $product = new $model;
        $productTable = $product->getTable();
        $productMorphClass = $product->getMorphClass();

        $query->whereExists(function ($query) use ($teamId, $taxonomyItemUuids, $productTable, $productMorphClass): void {
            $query->selectRaw('1')
                ->from('taxonomyables')
                ->join('taxonomy_items', 'taxonomyables.taxonomy_item_id', '=', 'taxonomy_items.id')
                ->join('taxonomies', 'taxonomy_items.taxonomy_id', '=', 'taxonomies.id')
                ->whereColumn('taxonomyables.taxonomyable_id', $productTable.'.id')
                ->where('taxonomyables.taxonomyable_type', $productMorphClass)
                ->where('taxonomyables.team_id', $teamId)
                ->where('taxonomy_items.team_id', $teamId)
                ->whereIn('taxonomy_items.uuid', $taxonomyItemUuids)
                ->where('taxonomies.team_id', $teamId);
        });
    }

    private function newsContentSource(string $type): string
    {
        $fallback = match ($type) {
            'featured_news' => 'featured',
            'taxonomy_news' => 'taxonomy',
            default => 'all',
        };

        $source = (string) data_get($this->section, 'settings.content_source', $fallback);

        return in_array($source, ['all', 'featured', 'taxonomy'], true) ? $source : $fallback;
    }

    private function productContentSource(string $type): string
    {
        $fallback = $type === 'featured_products' ? 'featured' : 'all';
        $source = (string) data_get($this->section, 'settings.content_source', $fallback);

        return in_array($source, ['all', 'featured', 'taxonomy'], true) ? $source : $fallback;
    }

    private function galleryContentSource(string $type): string
    {
        $source = (string) data_get($this->section, 'settings.content_source', 'albums');

        return in_array($source, ['albums', 'selected_gallery', 'direct'], true) ? $source : 'albums';
    }

    private function usesLoadableCarousel(string $key): bool
    {
        if (in_array($key, ['featured_products', 'all_products', 'gallery', 'gallery_grid'], true)) {
            return $this->usesRecordsCarousel($key);
        }

        if ($this->usesSectionItemCarousel($key)) {
            return true;
        }

        return $key === 'calendar'
            && (string) data_get($this->section, 'type') === 'calendar'
            && (string) data_get($this->section, 'settings.layout_variant') === 'calendar-carousel';
    }

    private function usesSectionItemCarousel(string $key): bool
    {
        return $key === 'testimonials'
            && (string) data_get($this->section, 'type') === 'testimonials'
            && (string) data_get($this->section, 'settings.layout_variant') === 'carousel';
    }

    private function supportsLoadMore(string $type): bool
    {
        return in_array($type, ['featured_products', 'all_products', 'featured_news', 'latest_news', 'taxonomy_news', 'gallery', 'gallery_grid'], true);
    }

    private function usesRecordsCarousel(string $type): bool
    {
        if (! in_array($type, ['featured_products', 'all_products', 'gallery', 'gallery_grid'], true)) {
            return false;
        }

        return (string) data_get($this->section, 'settings.layout_variant') === 'carousel';
    }

    /** @return HasMany<SectionItem, Section> */
    private function sectionItemsQuery(): HasMany
    {
        return ($this->sectionModel() ?? new Section)
            ->visibleItems()
            ->with(['galleries.featuredMedia', 'galleries.firstMedia']);
    }

    /** @return EloquentCollection<int, SectionItem> */
    private function cachedSectionItems(): EloquentCollection
    {
        if ($this->sectionModel() === null) {
            return new EloquentCollection;
        }

        return $this->rememberSectionModels(
            'section-items',
            PagesModels::sectionItem(),
            [],
            false,
            fn (): EloquentCollection => $this->sectionItemsQuery()->get(),
        );
    }

    /** @return array<string, mixed> */
    private function recordsCacheContext(string $type, ?int $limit = null): array
    {
        return [
            'type' => $type,
            'limit' => $limit,
            'settings' => (array) data_get($this->section, 'settings', []),
            'category_filter' => $this->activeCategoryFilter(),
            'tag_filter' => $this->activeTagFilter(),
            'time_bucket' => now()->format('YmdH'),
        ];
    }

    /** @return Collection<int, SectionItem> */
    private function orderedCalendarEvents(): Collection
    {
        if ($this->emptyStatePreviewEnabled()) {
            return collect();
        }

        $items = $this->cachedSectionItems();

        return $items
            ->filter(fn ($item): bool => $this->calendarDate(data_get($item, 'settings.event_date')) instanceof Carbon)
            ->sortBy(fn ($item): string => implode('|', [
                $this->calendarDate(data_get($item, 'settings.event_date'))?->format('Y-m-d') ?? '9999-12-31',
                $this->normalizedEventTime(data_get($item, 'settings.starts_at')) ?? '99:99',
                str_pad((string) ((int) data_get($item, 'sort_order', 0)), 6, '0', STR_PAD_LEFT),
            ]))
            ->values();
    }

    private function calendarDate(mixed $eventDate): ?Carbon
    {
        if (! is_string($eventDate) || trim($eventDate) === '') {
            return null;
        }

        try {
            return Carbon::parse($eventDate)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizedEventTime(mixed $time): ?string
    {
        if (! is_string($time) || trim($time) === '') {
            return null;
        }

        $time = trim($time);

        if (preg_match('/^\d{2}:\d{2}$/', $time) === 1) {
            return $time;
        }

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time) === 1) {
            return substr($time, 0, 5);
        }

        try {
            return Carbon::parse($time)->format('H:i');
        } catch (\Throwable) {
            return null;
        }
    }

    private function resolvedTeamId(): ?int
    {
        $teamId = data_get($this->page, 'team_id') ?? data_get($this->section, 'team_id');

        return is_numeric($teamId) ? (int) $teamId : null;
    }

    private function sectionModel(): ?Section
    {
        return $this->section instanceof Section ? $this->section : null;
    }

    /** @return class-string<Post> */
    private function postModel(): string
    {
        $model = config('blog.models.post', Post::class);

        return is_string($model) && is_a($model, Post::class, true) ? $model : Post::class;
    }
}
