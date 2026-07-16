<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Tests;

use IvanBaric\NivaTemplate\NivaTemplateServiceProvider;
use IvanBaric\Pages\PagesServiceProvider;
use IvanBaric\TemplateEngine\TemplateEngineServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            TemplateEngineServiceProvider::class,
            PagesServiceProvider::class,
            NivaTemplateServiceProvider::class,
        ];
    }
}
