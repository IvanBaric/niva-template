<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use IvanBaric\Blog\Models\Post;
use IvanBaric\Gallery\Events\GalleryMediaDeleted;
use IvanBaric\Gallery\Events\GalleryMediaFeatured;
use IvanBaric\Gallery\Events\GalleryMediaMetaUpdated;
use IvanBaric\Gallery\Events\GalleryMediaReordered;
use IvanBaric\Gallery\Events\GalleryMediaUploaded;
use IvanBaric\Gallery\Support\GalleryModels;
use IvanBaric\NivaTemplate\Admin\PageSections;
use IvanBaric\NivaTemplate\Observers\PublicSectionCacheObserver;
use IvanBaric\NivaTemplate\Support\NivaTemplateModels;
use IvanBaric\NivaTemplate\Support\PublicSectionCache;
use IvanBaric\Pages\Events\SectionItemsReordered;
use IvanBaric\Pages\Support\PagesModels;
use IvanBaric\Taxonomy\Events\TaxonomyItemAttached;
use IvanBaric\Taxonomy\Events\TaxonomyItemDetached;
use IvanBaric\Taxonomy\Events\TaxonomyItemsSynced;
use IvanBaric\Taxonomy\Support\TaxonomyModels;

final class NivaTemplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/niva-template.php', 'niva-template');
        $this->app->singleton(PublicSectionCache::class);

        /** @var array<string, mixed> $definition */
        $definition = require __DIR__.'/../config/templates.php';

        if ((bool) config('niva-template.registration.templates', true)) {
            $this->registerTemplates($definition);
        }

        if ((bool) config('niva-template.registration.admin_sections', true)) {
            $sources = (array) config('pages.admin_section_definitions', []);

            if (! in_array(PageSections::class, $sources, true)) {
                $sources[] = PageSections::class;
                config()->set('pages.admin_section_definitions', $sources);
            }
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'niva-template');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'niva-template');

        $this->publishes([
            __DIR__.'/../config/niva-template.php' => config_path('niva-template.php'),
        ], 'niva-template-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/niva-template'),
        ], 'niva-template-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/niva-template'),
        ], 'niva-template-translations');

        $this->registerPublicSectionCacheInvalidation();
    }

    private function registerPublicSectionCacheInvalidation(): void
    {
        $models = [
            PagesModels::page(),
            PagesModels::section(),
            PagesModels::sectionItem(),
            NivaTemplateModels::organization(),
            NivaTemplateModels::product(),
            config('blog.models.post', Post::class),
            GalleryModels::gallery(),
            GalleryModels::media(),
            TaxonomyModels::taxonomy(),
            TaxonomyModels::taxonomyItem(),
        ];

        collect($models)
            ->filter(fn (mixed $model): bool => is_string($model) && is_a($model, Model::class, true))
            ->unique()
            ->each(function (string $model): void {
                $model::observe(PublicSectionCacheObserver::class);
            });

        Event::listen(
            [TaxonomyItemAttached::class, TaxonomyItemDetached::class, TaxonomyItemsSynced::class],
            function (TaxonomyItemAttached|TaxonomyItemDetached|TaxonomyItemsSynced $event): void {
                app(PublicSectionCacheObserver::class)->invalidate($event->model);
            },
        );

        Event::listen(
            SectionItemsReordered::class,
            function (SectionItemsReordered $event): void {
                app(PublicSectionCacheObserver::class)->invalidate($event->section);
            },
        );

        Event::listen(
            [
                GalleryMediaDeleted::class,
                GalleryMediaFeatured::class,
                GalleryMediaMetaUpdated::class,
                GalleryMediaReordered::class,
                GalleryMediaUploaded::class,
            ],
            function (GalleryMediaDeleted|GalleryMediaFeatured|GalleryMediaMetaUpdated|GalleryMediaReordered|GalleryMediaUploaded $event): void {
                app(PublicSectionCacheObserver::class)->invalidate($event->gallery);
            },
        );
    }

    /** @param array<string, mixed> $definition */
    private function registerTemplates(array $definition): void
    {
        $packageTemplates = (array) ($definition['templates'] ?? []);
        $hostTemplates = (array) config('template_engine.templates', []);
        config()->set('template_engine.templates', array_replace_recursive($packageTemplates, $hostTemplates));

        $pageTemplates = (array) config('pages.templates', []);
        foreach ($packageTemplates as $key => $template) {
            $pageTemplates[$key] ??= ['label' => (string) data_get($template, 'label', $key)];
        }
        config()->set('pages.templates', $pageTemplates);

        foreach (['only_components', 'exclude_components', 'exclude_types'] as $key) {
            $path = "template_engine.performance.lazy_sections.{$key}";
            if ((array) config($path, []) === []) {
                config()->set($path, (array) data_get($definition, "performance.lazy_sections.{$key}", []));
            }
        }
    }
}
