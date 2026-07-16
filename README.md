# Niva Template

Reusable public website implementation for the IvanBaric Pages and Template Engine packages. It contains the concrete visual system that used to live in Niva's `App` namespace.

## Included

- `niva-classic` and `niva-modern` Template Engine registrations
- all 27 supported section types and their public empty states
- eight interchangeable desktop headers
- hierarchical desktop dropdown navigation and mobile navigation
- reusable footer with parent and child page links
- public renderers for section items, products, posts and galleries
- complete Pages admin section catalog used by `Uredi sekciju`
- package-owned field option providers for galleries and taxonomies
- default `niva-template::public-layout` for a new public site
- Croatian and English template translations
- optional public language switcher rendered consistently in every desktop and mobile header variant

Pages continues to own pages, subpages, sections, items, reorder and public editing. Template Engine continues to own template registration and rendering infrastructure. This package is the concrete public-site implementation layered on top of both.

## Installation

```bash
composer config repositories.niva-template vcs git@github.com:IvanBaric/niva-template.git
composer require ivanbaric/niva-template:dev-main
php artisan vendor:publish --tag=niva-template-config
```

The service provider is auto-discovered. It registers both templates and appends `IvanBaric\NivaTemplate\Admin\PageSections` to `pages.admin_section_definitions`.

Configure host models in `config/niva-template.php`:

```php
return [
    'models' => [
        'organization' => App\Models\Organization::class,
        'product' => App\Models\Product::class,
    ],
    'urls' => [
        'admin_resolver' => App\Support\AdminEntryUrl::class,
    ],
];
```

The organization model is expected to expose the conventional `team_id`, `slug`, `settings`, contact fields and optional `localized()`, logo and header-image methods. Product tenant, visibility, featured, publication and ordering columns plus eager-loaded relationships are configurable under `products`; the model only needs localized fields and an optional featured-image method. A project without products may leave the product model `null`; product sections then return no records.

For a package-owned public shell:

```php
// config/pages.php
'public_site' => [
    'enabled' => true,
    'layout' => 'niva-template::public-layout',
    // subject model, route and content providers...
],
```

Set `niva-template.layout.head_view` when the host needs Vite assets, SEO tags, fonts or a favicon. A project may keep its own layout while still using every template component.

## Extension

Host template definitions override package definitions with the same key. Set either registration flag to `false` when replacing templates or admin section definitions:

```php
'registration' => [
    'templates' => false,
    'admin_sections' => false,
],
```

Views and translations are publishable through `niva-template-views` and `niva-template-translations`. Prefer configuration and host adapters before publishing views so upgrades remain straightforward.

Set `niva-template.language_switcher.component` to a Livewire component such as `IvanBaric\Language\Livewire\AppLanguageSwitcher` to render the same locale control in all eight headers and the mobile menu. The dependency is optional and the shell renders no switcher when the component is not configured.

## Reuse Checklist

1. Install Corexis, Velora, Pages, Template Engine, Gallery, Taxonomy, Blog and this package.
2. Configure the tenant resolver and Pages public subject/route.
3. Configure organization and optional product models.
4. Choose `niva-template::public-layout` or a host layout that mounts package Header and Footer.
5. Configure Pages content providers for products, posts and galleries.
6. Run `php artisan template-engine:doctor` and the application's public rendering tests.
