<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Tests\Feature;

use IvanBaric\NivaTemplate\Admin\PageSections;
use IvanBaric\NivaTemplate\Templates\Classic\Livewire\GenericSection;
use IvanBaric\NivaTemplate\Tests\TestCase;

final class NivaTemplateRegistrationTest extends TestCase
{
    public function test_it_registers_templates_admin_sections_views_and_translations(): void
    {
        $classic = config('template_engine.templates.niva-classic');

        $this->assertIsArray($classic);
        $this->assertSame(GenericSection::class, data_get($classic, 'sections.about.component'));
        $this->assertCount(27, data_get($classic, 'sections'));
        $this->assertContains(PageSections::class, config('pages.admin_section_definitions'));
        $this->assertTrue(view()->exists('niva-template::templates.classic.header'));
        $this->assertTrue(view()->exists('niva-template::components.public-layout'));
        $this->assertSame('Uvodni blok', trans('niva-template::niva.sections.hero', locale: 'hr'));
    }
}
