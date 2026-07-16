<?php

return [
    'models' => [
        'organization' => null,
        'product' => null,
    ],

    'organization' => [
        'team_column' => 'team_id',
        'active_column' => 'is_active',
        'active_value' => true,
    ],

    'products' => [
        'team_column' => 'team_id',
        'visible_column' => 'is_visible',
        'featured_column' => 'is_featured',
        'published_column' => 'published_at',
        'order_column' => 'sort_order',
        'eager_load' => ['galleries.featuredMedia', 'galleries.firstMedia'],
    ],

    'urls' => [
        'admin_resolver' => null,
    ],

    'media' => [
        'organization_logo_collection' => 'website_logo',
        'organization_header_collection' => 'website_header_image',
        'organization_mobile_header_collection' => 'website_mobile_header_image',
    ],

    'layout' => [
        'head_view' => null,
    ],

    'language_switcher' => [
        'component' => null,
        'show_flags' => true,
    ],

    'registration' => [
        'templates' => true,
        'admin_sections' => true,
    ],
];
