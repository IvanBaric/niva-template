@php
    $quoteText = trim((string) ($statement ?? ''));
    $widthClass = (string) ($widthClass ?? 'max-w-2xl');
    $textRoleClass = (string) ($textRoleClass ?? 'cx-public-quote-featured');
    $textClass = (string) ($textClass ?? '');
    $quoteClass = (string) ($quoteClass ?? '');
    $showQuoteMark = (bool) ($showQuoteMark ?? true);
@endphp

@if ($quoteText !== '')
    <blockquote class="{{ $widthClass }} relative border-s-2 border-[color:var(--niva-primary-300)] py-1 ps-6 dark:border-[color:var(--niva-primary-700)] {{ $quoteClass }}">
        @if ($showQuoteMark)
            <span class="pointer-events-none absolute -top-4 start-5 select-none text-6xl font-semibold leading-none text-[color:var(--niva-primary-100)] dark:text-[color:var(--niva-primary-900)]" aria-hidden="true">&ldquo;</span>
        @endif
        <p @class([
            'relative text-zinc-900 dark:text-zinc-100',
            $textRoleClass,
            $textClass,
        ])>{{ $quoteText }}</p>
    </blockquote>
@endif
