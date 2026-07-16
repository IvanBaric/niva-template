<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Support;

final class AdminUrl
{
    public function resolve(): ?string
    {
        $resolver = config('niva-template.urls.admin_resolver');

        if (! is_string($resolver) || $resolver === '' || ! class_exists($resolver)) {
            return null;
        }

        $instance = app()->make($resolver);

        if (! is_object($instance) || ! method_exists($instance, 'url')) {
            return null;
        }

        $url = $instance->url();

        return is_string($url) && $url !== '' ? $url : null;
    }
}
