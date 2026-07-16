<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Templates\Modern\Livewire;

use Illuminate\Contracts\View\View;
use IvanBaric\TemplateEngine\Livewire\BaseTemplateSectionComponent;

final class HeroSection extends BaseTemplateSectionComponent
{
    public function render(): View
    {
        return view('niva-template::templates.modern.hero-section');
    }
}
