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

    'public_section_cache' => [
        'enabled' => env('NIVA_PUBLIC_SECTION_CACHE', env('APP_ENV') !== 'testing'),
        'store' => env('NIVA_PUBLIC_SECTION_CACHE_STORE'),
        'namespace' => env('NIVA_PUBLIC_SECTION_CACHE_NAMESPACE', 'niva-public-sections'),
        'schema_version' => 1,
        'static_ttl' => (int) env('NIVA_PUBLIC_SECTION_CACHE_STATIC_TTL', 31_536_000),
        'dynamic_ttl' => (int) env('NIVA_PUBLIC_SECTION_CACHE_DYNAMIC_TTL', 3_600),
        'lock_seconds' => (int) env('NIVA_PUBLIC_SECTION_CACHE_LOCK_SECONDS', 10),
        'lock_wait_seconds' => (int) env('NIVA_PUBLIC_SECTION_CACHE_LOCK_WAIT_SECONDS', 3),
    ],
];
