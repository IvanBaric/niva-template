<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Templates\Classic\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use IvanBaric\Pages\Livewire\Concerns\CyclesSingletonLayoutVariants;
use IvanBaric\Pages\Support\OnePageNavigation;
use IvanBaric\Pages\Support\PublicSiteUrl;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

final class Footer extends Component
{
    use CyclesSingletonLayoutVariants;

    #[Locked]
    public mixed $organization = null;

    #[Locked]
    public mixed $publicPages = null;

    public function mount(mixed $organization = null, mixed $publicPages = null): void
    {
        $this->organization = $organization;
        $this->publicPages = $publicPages;
    }

    public function canCycleFooterVariant(): bool
    {
        return $this->canCycleSingletonLayoutVariant('template_footer');
    }

    public function cycleFooterVariant(string $direction = 'next'): void
    {
        $this->cycleSingletonLayoutVariant('template_footer', $direction);
    }

    public function nextFooterVariantLabel(): string
    {
        return $this->nextSingletonLayoutVariantLabel('template_footer');
    }

    public function previousFooterVariantLabel(): string
    {
        return $this->previousSingletonLayoutVariantLabel('template_footer');
    }

    public function canEditFooter(): bool
    {
        $user = auth()->user();
        $organizationTeamId = data_get($this->organization, 'team_id');
        $currentTeamId = data_get($user, 'current_team_id');

        if (! $user || ! $this->organization instanceof Model || ! is_numeric($organizationTeamId) || ! is_numeric($currentTeamId)) {
            return false;
        }

        if ((int) $organizationTeamId !== (int) $currentTeamId) {
            return false;
        }

        return corexis_can('pages.update', $this->organization);
    }

    #[On('pages-public-template-part-updated.footer')]
    public function refreshEditedFooter(): void
    {
        if ($this->organization instanceof Model) {
            $this->organization = $this->organization->refresh();
        }
    }

    public function organizationName(): string
    {
        return is_object($this->organization) && method_exists($this->organization, 'localized')
            ? $this->organization->localized('name')
            : config('app.name', 'Niva');
    }

    public function organizationDescription(): string
    {
        return $this->settingText('description');
    }

    public function logoUrl(): ?string
    {
        if (! is_object($this->organization) || ! method_exists($this->organization, 'websiteLogoUrl')) {
            return null;
        }

        return $this->organization->websiteLogoUrl();
    }

    public function institutionName(): string
    {
        return trim((string) data_get($this->organization, 'settings.institution_name', ''));
    }

    /** @return array<int, array{label: string, value: string, href: string|null}> */
    public function contactItems(): array
    {
        return collect([
            [
                'label' => __('E-pošta'),
                'value' => trim((string) data_get($this->organization, 'email', '')),
                'href' => filled(data_get($this->organization, 'email')) ? 'mailto:'.trim((string) data_get($this->organization, 'email')) : null,
            ],
            [
                'label' => __('Telefon'),
                'value' => trim((string) data_get($this->organization, 'phone', '')),
                'href' => filled(data_get($this->organization, 'phone')) ? 'tel:'.preg_replace('/\s+/', '', trim((string) data_get($this->organization, 'phone'))) : null,
            ],
            [
                'label' => __('Adresa'),
                'value' => $this->locationText(),
                'href' => null,
            ],
        ])
            ->filter(fn (array $item): bool => filled($item['value']))
            ->values()
            ->all();
    }

    public function copyrightText(): string
    {
        $copyright = data_get($this->settings(), 'copyright');

        if (is_string($copyright) && trim($copyright) !== '') {
            return trim($copyright);
        }

        return __('© :year :name. Sva prava pridržana.', [
            'year' => now()->year,
            'name' => $this->organizationName(),
        ]);
    }

    public function footerVariant(): string
    {
        $variant = data_get($this->settings(), 'layout_variant');

        return $variant === 'classic' ? 'classic' : 'classic';
    }

    /** @return array<int, array{label: string, href: string, children: array<int, array{label: string, href: string}>}> */
    public function navItems(): array
    {
        $onePageItems = app(OnePageNavigation::class)->navItems($this->pages(), $this->organizationUrl());

        if ($onePageItems !== []) {
            return collect($onePageItems)
                ->map(fn (array $item): array => [
                    'label' => $item['label'],
                    'href' => $item['href'],
                    'children' => [],
                ])
                ->values()
                ->all();
        }

        $pages = $this->pages();

        return $pages
            ->whereNull('parent_id')
            ->map(fn (mixed $page): array => [
                'label' => $this->pageLabel($page),
                'href' => $this->pageUrl($page),
                'children' => $pages
                    ->where('parent_id', data_get($page, 'id'))
                    ->map(fn (mixed $child): array => [
                        'label' => $this->pageLabel($child),
                        'href' => $this->pageUrl($child),
                    ])
                    ->filter(fn (array $item): bool => filled($item['label']))
                    ->values()
                    ->all(),
            ])
            ->filter(fn (array $item): bool => filled($item['label']))
            ->values()
            ->all();
    }

    public function render(): View
    {
        return view('niva-template::templates.classic.footer');
    }

    public function organizationUrl(?string $pageSlug = null): string
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

    /** @return Collection<int, mixed> */
    private function pages(): Collection
    {
        return is_iterable($this->publicPages)
            ? collect($this->publicPages)->values()
            : collect();
    }

    private function locationText(): string
    {
        $parts = array_filter([
            trim((string) data_get($this->organization, 'address', '')),
            trim(implode(' ', array_filter([
                trim((string) data_get($this->organization, 'postal_code', '')),
                trim((string) data_get($this->organization, 'city', '')),
            ]))),
        ]);

        return implode(', ', $parts);
    }

    /** @return array<string, mixed> */
    private function settings(): array
    {
        return (array) data_get($this->organization, 'settings.templates.niva-classic.footer', []);
    }

    private function settingText(string $key): string
    {
        $value = data_get($this->settings(), $key);

        if (is_array($value)) {
            $locale = app()->getLocale();
            $fallback = config('app.fallback_locale', 'en');
            $value = $value[$locale] ?? $value[$fallback] ?? reset($value) ?: '';
        }

        return is_string($value) ? trim($value) : '';
    }
}
