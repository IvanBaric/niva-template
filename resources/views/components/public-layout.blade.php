@props([
    'title' => null,
    'subject' => null,
    'organization' => null,
    'publicPages' => null,
    'templateKey' => null,
    'smallHeader' => false,
])

@php
    $organization ??= $subject;
    $name = is_object($organization) && method_exists($organization, 'localized')
        ? trim((string) $organization->localized('name'))
        : trim((string) config('app.name'));
    $documentTitle = collect([$name, trim((string) $title)])->filter()->unique()->implode(' - ');
    $teamId = data_get($organization, 'team_id');
    $tenantId = auth()->check() ? corexis_tenant_id() : null;
    $canManage = auth()->check()
        && is_numeric($teamId)
        && is_numeric($tenantId)
        && (string) $teamId === (string) $tenantId
        && corexis_can('pages.view', $organization);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $documentTitle }}</title>
        @if (filled(config('niva-template.layout.head_view')))
            @include((string) config('niva-template.layout.head_view'), compact('organization', 'documentTitle'))
        @endif
        @stack('head')
    </head>
    <body data-corexis-public-shell class="min-h-screen bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white">
        <a href="#main-content" class="cx-public-skip-link">{{ __('Preskoči na sadržaj') }}</a>

        @livewire(\IvanBaric\NivaTemplate\Templates\Classic\Livewire\Header::class, [
            'organization' => $organization,
            'publicPages' => $publicPages,
            'small' => (bool) $smallHeader,
        ], key('niva-template-header-'.data_get($organization, 'slug').($smallHeader ? '-small' : '-large')))

        <div id="main-content" tabindex="-1">
            {{ $slot }}
        </div>

        @livewire(\IvanBaric\NivaTemplate\Templates\Classic\Livewire\Footer::class, [
            'organization' => $organization,
            'publicPages' => $publicPages,
        ], key('niva-template-footer-'.data_get($organization, 'slug')))

        @if ($canManage && (bool) config('pages.public_management.enabled', false))
            <livewire:pages.public.management-flyout />
        @endif

        @stack('body')
        @fluxScripts
    </body>
</html>
