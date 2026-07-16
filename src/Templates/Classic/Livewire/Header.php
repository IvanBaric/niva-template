<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Templates\Classic\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use IvanBaric\NivaTemplate\Support\AdminUrl;
use IvanBaric\Pages\Support\OnePageNavigation;
use IvanBaric\Pages\Support\PagesModels;
use IvanBaric\Pages\Support\PublicSiteUrl;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

final class Header extends Component
{
    private const HEADER_VARIANTS = [
        'header-1' => 'Zaglavlje 1',
        'header-2' => 'Zaglavlje 2',
        'header-3' => 'Zaglavlje 3',
        'header-4' => 'Zaglavlje 4',
        'header-5' => 'Zaglavlje 5',
        'header-6' => 'Zaglavlje 6',
        'header-7' => 'Zaglavlje 7',
        'header-8' => 'Zaglavlje 8',
    ];

    #[Locked]
    public mixed $organization = null;

    #[Locked]
    public mixed $publicPages = null;

    #[Locked]
    public bool $small = false;

    #[Locked]
    public ?string $titleOverride = null;

    #[Locked]
    public ?string $subtitleOverride = null;

    public function mount(mixed $organization = null, mixed $publicPages = null, bool $small = false, ?string $titleOverride = null, ?string $subtitleOverride = null): void
    {
        $this->organization = $organization;
        $this->publicPages = $publicPages;
        $this->small = $small;
        $this->titleOverride = $titleOverride;
        $this->subtitleOverride = $subtitleOverride;
    }

    public function homeUrl(): string
    {
        return $this->organizationUrl();
    }

    public function adminUrl(): ?string
    {
        return app(AdminUrl::class)->resolve();
    }

    public function headerEditUrl(): ?string
    {
        if (! $this->canManagePublicTemplate() || ! Route::has('admin.pages.index')) {
            return null;
        }

        return route('admin.pages.index', ['part' => 'header']);
    }

    public function canAccessPublicManagement(): bool
    {
        $user = Auth::user();
        $teamId = data_get($this->organization, 'team_id');
        $currentTeamId = data_get($user, 'current_team_id');

        return $user !== null
            && is_numeric($teamId)
            && is_numeric($currentTeamId)
            && (int) $teamId === (int) $currentTeamId
            && corexis_can('pages.view', $this->organization);
    }

    public function canCycleHeaderVariant(): bool
    {
        return $this->canManagePublicTemplate() && is_object($this->organization);
    }

    public function previousHeaderVariantLabel(): string
    {
        return self::HEADER_VARIANTS[$this->adjacentHeaderVariant('previous')] ?? __('Prethodni izgled');
    }

    public function nextHeaderVariantLabel(): string
    {
        return self::HEADER_VARIANTS[$this->adjacentHeaderVariant('next')] ?? __('Sljedeći izgled');
    }

    public function cycleHeaderVariant(string $direction = 'next'): void
    {
        abort_unless($this->canCycleHeaderVariant(), 403);
        abort_unless($this->organization instanceof Model, 404);
        corexis_authorize('pages.update', $this->organization);

        $variant = $this->adjacentHeaderVariant($direction === 'previous' ? 'previous' : 'next');
        $settings = (array) data_get($this->organization, 'settings', []);

        data_set($settings, 'templates.niva-classic.header.header_variant', $variant);

        $this->organization->forceFill(['settings' => $settings])->save();
        $this->organization = $this->organization->refresh();
    }

    #[On('pages-public-template-part-updated.header')]
    public function refreshEditedHeader(): void
    {
        if ($this->organization instanceof Model) {
            $this->organization = $this->organization->refresh();
        }
    }

    #[On('pages-public-structure-updated')]
    public function refreshPublicPages(): void
    {
        $teamId = data_get($this->organization, 'team_id');

        if (! is_numeric($teamId)) {
            $this->publicPages = collect();

            return;
        }

        $pageModel = PagesModels::page();
        $this->publicPages = $pageModel::query()
            ->forTenant((int) $teamId)
            ->published()
            ->navigationVisible()
            ->ordered()
            ->get();
    }

    /** @return array<int, array{label: string, href: string, active: bool}> */
    public function navItems(): array
    {
        $onePageItems = app(OnePageNavigation::class)->navItems($this->pages(), $this->organizationUrl());

        if ($onePageItems !== []) {
            return $onePageItems;
        }

        $pages = $this->pages();
        $pageIds = $pages->pluck('id')->filter()->map('strval');

        return $pages
            ->filter(fn (mixed $page): bool => ! filled(data_get($page, 'parent_id'))
                || ! $pageIds->contains((string) data_get($page, 'parent_id')))
            ->map(function (mixed $page) use ($pages): array {
                $children = $pages
                    ->filter(fn (mixed $child): bool => (string) data_get($child, 'parent_id') === (string) data_get($page, 'id'))
                    ->map(fn (mixed $child): array => [
                        'label' => $this->pageLabel($child),
                        'href' => $this->pageUrl($child),
                        'active' => url()->current() === $this->pageUrl($child),
                    ])
                    ->filter(fn (array $item): bool => filled($item['label']))
                    ->values()
                    ->all();

                return [
                    'label' => $this->pageLabel($page),
                    'href' => $this->pageUrl($page),
                    'active' => url()->current() === $this->pageUrl($page)
                        || collect($children)->contains('active', true),
                    'children' => $children,
                ];
            })
            ->filter(fn (array $item): bool => filled($item['label']))
            ->values()
            ->all();
    }

    public function organizationName(): string
    {
        return is_object($this->organization) && method_exists($this->organization, 'localized')
            ? $this->organization->localized('name')
            : config('app.name', 'Niva');
    }

    public function institutionName(): string
    {
        $institution = trim((string) data_get($this->organization, 'settings.institution_name', ''));

        return $institution !== '' ? $institution : '';
    }

    public function eyebrow(): string
    {
        $settings = $this->settings();
        $eyebrow = trim((string) ($settings['eyebrow'] ?? ''));

        if (array_key_exists('eyebrow', $settings) && $eyebrow !== '') {
            return $eyebrow;
        }

        return $this->institutionName();
    }

    public function title(): string
    {
        if ($this->titleOverride !== null) {
            return $this->titleOverride;
        }

        $settings = $this->settings();
        $title = trim((string) ($settings['title'] ?? ''));

        if (array_key_exists('title', $settings) && $title !== '') {
            return $title;
        }

        return $this->organizationName();
    }

    public function subtitle(): string
    {
        if ($this->subtitleOverride !== null) {
            return $this->subtitleOverride;
        }

        $settings = $this->settings();

        if (array_key_exists('subtitle', $settings)) {
            return (string) ($settings['subtitle'] ?? '');
        }

        return is_object($this->organization) && method_exists($this->organization, 'localized')
            ? $this->organization->localized('description')
            : '';
    }

    public function showLogo(): bool
    {
        $value = data_get($this->settings(), 'show_logo');

        return $value === null ? true : (bool) $value;
    }

    public function logoUrl(): ?string
    {
        return is_object($this->organization) && method_exists($this->organization, 'websiteLogoUrl')
            ? $this->organization->websiteLogoUrl()
            : null;
    }

    public function imageUrl(): ?string
    {
        if (is_object($this->organization) && method_exists($this->organization, 'websiteHeaderImageUrl')) {
            $url = $this->organization->websiteHeaderImageUrl();

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return $this->storedImageUrl('image');
    }

    public function mobileImageUrl(): ?string
    {
        if (is_object($this->organization) && method_exists($this->organization, 'websiteMobileHeaderImageUrl')) {
            $url = $this->organization->websiteMobileHeaderImageUrl();

            if (is_string($url) && $url !== '') {
                return $url;
            }
        }

        return $this->storedImageUrl('mobile_image');
    }

    /**
     * @return array<string, string>
     */
    public function imageSources(): array
    {
        if (is_object($this->organization) && method_exists($this->organization, 'websiteHeaderImageSources')) {
            $sources = $this->organization->websiteHeaderImageSources();

            if (is_array($sources) && $sources !== []) {
                return $sources;
            }
        }

        $url = $this->storedImageUrl('image');

        return $url ? ['fallback' => $url] : [];
    }

    /**
     * @return array<string, string>
     */
    public function mobileImageSources(): array
    {
        if (is_object($this->organization) && method_exists($this->organization, 'websiteMobileHeaderImageSources')) {
            $sources = $this->organization->websiteMobileHeaderImageSources();

            if (is_array($sources) && $sources !== []) {
                return $sources;
            }
        }

        $url = $this->storedImageUrl('mobile_image');

        return $url ? ['fallback' => $url] : [];
    }

    /**
     * @return array<string, string>
     */
    public function defaultImageSources(): array
    {
        return [
            'avif' => asset('images/defaults/niva-header-bracelets.avif'),
            'webp' => asset('images/defaults/niva-header-bracelets.webp'),
            'fallback' => asset('images/defaults/niva-header-bracelets.webp'),
        ];
    }

    /**
     * @return array<int, array{label: string, href: string, variant: string}>
     */
    public function ctaItems(): array
    {
        if ($this->small) {
            return [];
        }

        return collect([
            $this->ctaItem(1, __('Pogledaj radove'), 'primary', ['products'], ['products', 'radovi', 'proizvodi', 'radovi-i-rukotvorine']),
            $this->ctaItem(2, __('O zadruzi'), 'secondary', ['about'], ['about', 'o-zadruzi', 'o-nama']),
        ])
            ->filter()
            ->filter(fn (array $item): bool => filled($item['label']) && filled($item['href']))
            ->unique('href')
            ->values()
            ->all();
    }

    public function headerVariant(): string
    {
        $variant = data_get($this->settings(), 'header_variant');

        return array_key_exists((string) $variant, self::HEADER_VARIANTS) ? (string) $variant : 'header-1';
    }

    /** @return class-string<Component>|null */
    public function languageSwitcherComponent(): ?string
    {
        $component = config('niva-template.language_switcher.component');

        return is_string($component) && is_a($component, Component::class, true)
            ? $component
            : null;
    }

    public function render(): View
    {
        return view('niva-template::templates.classic.header');
    }

    private function storedImageUrl(string $key): ?string
    {
        $image = data_get($this->settings(), $key);

        if (! is_string($image) || $image === '') {
            return null;
        }

        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }

        return Storage::disk(corexis_public_media_disk())->url($image);
    }

    private function adjacentHeaderVariant(string $direction): string
    {
        $keys = array_keys(self::HEADER_VARIANTS);
        $current = $this->headerVariant();
        $index = array_search($current, $keys, true);
        $index = is_int($index) ? $index : 0;
        $offset = $direction === 'previous' ? -1 : 1;

        return $keys[($index + $offset + count($keys)) % count($keys)];
    }

    private function canManagePublicTemplate(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ((bool) data_get($user, 'is_superadmin')) {
            return true;
        }

        $teamId = data_get($this->organization, 'team_id');
        $currentTeamId = data_get($user, 'current_team_id');

        return $teamId !== null
            && $currentTeamId !== null
            && (string) $teamId === (string) $currentTeamId
            && corexis_can('pages.update', $this->organization);
    }

    private function organizationUrl(?string $pageSlug = null): string
    {
        return app(PublicSiteUrl::class)->page($this->organization, $pageSlug)
            ?? route('home');
    }

    private function pageUrl(mixed $page): string
    {
        $slug = (string) data_get($page, 'slug');

        if ((bool) data_get($page, 'is_home')) {
            return $this->organizationUrl();
        }

        $pageKey = (string) data_get($page, 'page_key', '');

        if ($pageKey === '') {
            $slug = (string) config('pages.public_slugs.'.$slug, $slug);
        }

        return $this->organizationUrl($slug);
    }

    private function pageLabel(mixed $page): string
    {
        return is_object($page) && method_exists($page, 'localized')
            ? $page->localized('title')
            : (string) data_get($page, 'title', data_get($page, 'slug'));
    }

    private function pageUrlForUuid(string $pageUuid): ?string
    {
        $page = $this->pages()
            ->first(fn (mixed $page): bool => (string) data_get($page, 'uuid', '') === $pageUuid);

        return $page ? $this->pageUrl($page) : null;
    }

    /**
     * @param  array<int, string>  $fallbackPageKeys
     * @param  array<int, string>  $fallbackSlugs
     * @return array{label: string, href: string|null, variant: string}|null
     */
    private function ctaItem(int $position, string $defaultLabel, string $variant, array $fallbackPageKeys, array $fallbackSlugs): ?array
    {
        $settings = $this->settings();
        $enabled = data_get($settings, 'cta.'.$position.'.enabled');

        if ($enabled !== null && ! (bool) $enabled) {
            return null;
        }

        $label = trim((string) data_get($settings, 'cta.'.$position.'.text', $defaultLabel));
        $pageUuid = trim((string) data_get($settings, 'cta.'.$position.'.page_uuid', ''));
        $href = $pageUuid !== '' ? $this->pageUrlForUuid($pageUuid) : null;

        if ($pageUuid === '') {
            $href = $this->pageUrlFor($fallbackPageKeys, $fallbackSlugs);
        }

        return [
            'label' => $label !== '' ? $label : $defaultLabel,
            'href' => $href,
            'variant' => $variant,
        ];
    }

    /**
     * @param  array<int, string>  $pageKeys
     * @param  array<int, string>  $slugs
     */
    private function pageUrlFor(array $pageKeys, array $slugs): ?string
    {
        $page = $this->pages()
            ->first(function (mixed $page) use ($pageKeys, $slugs): bool {
                $pageKey = (string) data_get($page, 'page_key', '');
                $slug = (string) data_get($page, 'slug', '');

                return in_array($pageKey, $pageKeys, true) || in_array($slug, $slugs, true);
            });

        return $page ? $this->pageUrl($page) : null;
    }

    /** @return Collection<int, mixed> */
    private function pages(): Collection
    {
        return is_iterable($this->publicPages)
            ? collect($this->publicPages)->values()
            : collect();
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return (array) data_get($this->organization, 'settings.templates.niva-classic.header', []);
    }
}
