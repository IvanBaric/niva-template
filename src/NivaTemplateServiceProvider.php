<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate;

use Illuminate\Support\ServiceProvider;
use IvanBaric\NivaTemplate\Admin\PageSections;

final class NivaTemplateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/niva-template.php', 'niva-template');

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
