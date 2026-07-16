<?php

use IvanBaric\NivaTemplate\Templates\Classic\Livewire\GenericSection as ClassicGenericSection;
use IvanBaric\NivaTemplate\Templates\Classic\Livewire\HeroSection as ClassicHeroSection;
use IvanBaric\NivaTemplate\Templates\Modern\Livewire\HeroSection as ModernHeroSection;

return [
    'default_template' => env('TEMPLATE_ENGINE_DEFAULT_TEMPLATE', 'niva-classic'),
    'strict' => env('TEMPLATE_ENGINE_STRICT', env('APP_DEBUG', false)),
    'context' => [
        'team_resolver' => null,
        'template_resolver' => null,
    ],
    'storage' => [
        'mode' => 'isolated',
        'shared_key' => '_shared',
        'templates_key' => '_templates',
        'remove_unknown_fields' => false,
        'preserve_other_template_data' => true,
    ],
    'performance' => [
        'lazy_sections' => [
            'enabled' => env('TEMPLATE_ENGINE_LAZY_SECTIONS', env('APP_ENV') !== 'testing'),
            'skip_first' => env('TEMPLATE_ENGINE_LAZY_SECTIONS_SKIP_FIRST', 1),
            'mode' => env('TEMPLATE_ENGINE_LAZY_SECTIONS_MODE', 'lazy'),
            'bundle' => env('TEMPLATE_ENGINE_LAZY_SECTIONS_BUNDLE', false),
            'only_components' => [
                ClassicGenericSection::class,
            ],
            'exclude_components' => [
                ClassicHeroSection::class,
                ModernHeroSection::class,
            ],
            'exclude_types' => ['hero'],
        ],
    ],
    'templates' => [
        'niva-modern' => [
            'label' => 'niva-template::niva.templates.modern.label',
            'enabled' => true,
            'order_strategy' => 'hybrid',
            'sections' => [
                'hero' => [
                    'label' => 'niva-template::niva.sections.hero',
                    'component' => ModernHeroSection::class,
                    'slot' => 'header',
                    'order' => 10,
                    'fields' => [
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'subtitle' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.subtitle', 'required' => false, 'rules' => ['string', 'max:500']],
                        'image' => ['type' => 'image', 'label' => 'niva-template::niva.fields.image', 'required' => false, 'rules' => ['nullable', 'string', 'max:2048']],
                    ],
                ],
            ],
        ],
        'niva-classic' => [
            'label' => 'niva-template::niva.templates.classic.label',
            'description' => 'niva-template::niva.templates.classic.description',
            'enabled' => true,
            'order_strategy' => 'hybrid',
            'sections' => [
                'hero' => [
                    'label' => 'niva-template::niva.sections.hero',
                    'component' => ClassicHeroSection::class,
                    'slot' => 'header',
                    'order' => 10,
                    'fields' => [
                        'badge' => ['type' => 'text', 'label' => 'niva-template::niva.fields.badge', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'subtitle' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.subtitle', 'required' => false, 'rules' => ['nullable', 'string', 'max:500']],
                        'image' => ['type' => 'image', 'label' => 'niva-template::niva.fields.image', 'required' => false, 'rules' => ['nullable', 'string', 'max:2048']],
                        'button_text' => ['type' => 'text', 'label' => 'niva-template::niva.fields.button_text', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'button_url' => ['type' => 'url', 'label' => 'niva-template::niva.fields.button_url', 'required' => false, 'rules' => ['nullable', 'url', 'max:255']],
                    ],
                ],
                'about' => [
                    'label' => 'niva-template::niva.sections.about',
                    'component' => ClassicGenericSection::class,
                    'slot' => 'main',
                    'order' => 20,
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'niva-template::niva.fields.eyebrow', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'description' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.description', 'required' => false, 'rules' => ['nullable', 'string', 'max:1200']],
                        'image' => ['type' => 'image', 'label' => 'niva-template::niva.fields.image', 'required' => false, 'rules' => ['nullable', 'string', 'max:2048']],
                        'button_text' => ['type' => 'text', 'label' => 'niva-template::niva.fields.button_text', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'button_url' => ['type' => 'url', 'label' => 'niva-template::niva.fields.button_url', 'required' => false, 'rules' => ['nullable', 'url', 'max:255']],
                    ],
                ],
                'featured_values' => ['label' => 'niva-template::niva.sections.featured_values', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 25],
                'content_blocks' => ['label' => 'niva-template::niva.sections.content_blocks', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 26],
                'collaboration' => ['label' => 'niva-template::niva.sections.collaboration', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 27],
                'features' => ['label' => 'niva-template::niva.sections.features', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 30],
                'statistics' => ['label' => 'niva-template::niva.sections.statistics', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 40],
                'featured_products' => ['label' => 'niva-template::niva.sections.featured_products', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 50],
                'featured_news' => ['label' => 'niva-template::niva.sections.featured_news', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 55],
                'latest_news' => ['label' => 'niva-template::niva.sections.latest_news', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 55],
                'taxonomy_news' => ['label' => 'Objave prema taxonomy', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 55],
                'testimonials' => ['label' => 'niva-template::niva.sections.testimonials', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 57],
                'gallery' => ['label' => 'niva-template::niva.sections.gallery', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 60],
                'photo_gallery' => ['label' => 'niva-template::niva.sections.photo_gallery', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 62],
                'video' => ['label' => 'niva-template::niva.sections.video', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 65],
                'calendar' => ['label' => 'niva-template::niva.sections.calendar', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 66],
                'partners' => ['label' => 'niva-template::niva.sections.partners', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 70],
                'faq' => ['label' => 'niva-template::niva.sections.faq', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 80],
                'mission' => [
                    'label' => 'niva-template::niva.sections.mission',
                    'component' => ClassicGenericSection::class,
                    'slot' => 'main',
                    'order' => 20,
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'niva-template::niva.fields.eyebrow', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'description' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.description', 'required' => false, 'rules' => ['nullable', 'string', 'max:1200']],
                    ],
                ],
                'vision' => [
                    'label' => 'niva-template::niva.sections.vision',
                    'component' => ClassicGenericSection::class,
                    'slot' => 'main',
                    'order' => 30,
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'niva-template::niva.fields.eyebrow', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'description' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.description', 'required' => false, 'rules' => ['nullable', 'string', 'max:1200']],
                    ],
                ],
                'values' => ['label' => 'niva-template::niva.sections.values', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 40],
                'team' => ['label' => 'niva-template::niva.sections.team', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 50],
                'all_products' => ['label' => 'niva-template::niva.sections.all_products', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 30],
                'how_to_order' => ['label' => 'niva-template::niva.sections.how_to_order', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 40],
                'gallery_grid' => ['label' => 'niva-template::niva.sections.gallery_grid', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 20],
                'contact' => [
                    'label' => 'niva-template::niva.sections.contact',
                    'component' => ClassicGenericSection::class,
                    'slot' => 'main',
                    'order' => 20,
                    'fields' => [
                        'eyebrow' => ['type' => 'text', 'label' => 'niva-template::niva.fields.eyebrow', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'title' => ['type' => 'text', 'label' => 'niva-template::niva.fields.title', 'required' => true, 'rules' => ['string', 'max:120']],
                        'description' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.description', 'required' => false, 'rules' => ['nullable', 'string', 'max:1200']],
                        'email' => ['type' => 'text', 'label' => 'niva-template::niva.fields.email', 'required' => false, 'rules' => ['nullable', 'email', 'max:255']],
                        'phone' => ['type' => 'text', 'label' => 'niva-template::niva.fields.phone', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'address' => ['type' => 'textarea', 'label' => 'niva-template::niva.fields.address', 'required' => false, 'rules' => ['nullable', 'string', 'max:500']],
                        'button_text' => ['type' => 'text', 'label' => 'niva-template::niva.fields.button_text', 'required' => false, 'rules' => ['nullable', 'string', 'max:80']],
                        'button_url' => ['type' => 'url', 'label' => 'niva-template::niva.fields.button_url', 'required' => false, 'rules' => ['nullable', 'url', 'max:255']],
                    ],
                ],
                'social_links' => ['label' => 'niva-template::niva.sections.social_links', 'component' => ClassicGenericSection::class, 'slot' => 'main', 'order' => 25],
            ],
        ],
    ],
];
