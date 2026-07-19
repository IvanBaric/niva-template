<?php

declare(strict_types=1);

namespace IvanBaric\NivaTemplate\Support;

use Illuminate\Support\Str;
use IvanBaric\Corexis\Support\PublicUrl;

final readonly class SocialLinks
{
    public function __construct(private PublicUrl $publicUrl) {}

    /** @return array<string, string> */
    public static function networkLabels(): array
    {
        return [
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube',
            'tiktok' => 'TikTok',
        ];
    }

    /** @return array<int, array{key: string, label: string, url: string, icon: string}> */
    public function fromOrganization(mixed $organization): array
    {
        return $this->fromSettings((array) data_get($organization, 'settings.social_links', []));
    }

    /** @return array<int, array{key: string, label: string, url: string, icon: string}> */
    public function fromSettings(array $settings): array
    {
        $links = [];

        if (array_is_list($settings)) {
            foreach ($settings as $entry) {
                $link = $this->normalizeEntry((array) $entry);

                if ($link !== null) {
                    $links[] = $link;
                }
            }

            return $links;
        }

        foreach (self::networkLabels() as $key => $label) {
            $entry = $settings[$key] ?? null;

            if (is_string($entry)) {
                $entry = ['url' => $entry];
            }

            $link = $this->normalizeEntry([
                ...((array) $entry),
                'key' => $key,
                'label' => data_get($entry, 'label', $label),
                'icon' => data_get($entry, 'icon', $key),
            ]);

            if ($link !== null) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /** @return array<int, array{key: string, label: string, url: string, icon: string}> */
    public function fromSectionItems(iterable $items): array
    {
        $links = [];

        foreach ($items as $item) {
            $icon = trim((string) data_get($item, 'icon'));
            $label = method_exists($item, 'localized')
                ? trim((string) $item->localized('title'))
                : trim((string) data_get($item, 'title'));

            $link = $this->normalizeEntry([
                'key' => $icon !== '' ? $icon : Str::of($label)->slug('_')->toString(),
                'label' => $label,
                'url' => data_get($item, 'url'),
                'icon' => $icon,
            ]);

            if ($link !== null) {
                $links[] = $link;
            }
        }

        return $links;
    }

    /** @return array<int, array{key: string, label: string, url: string, icon: string}> */
    public function fromLegacySettings(array $settings): array
    {
        return collect((array) data_get($settings, 'links', []))
            ->map(function (mixed $url, mixed $key): ?array {
                return $this->normalizeEntry([
                    'key' => (string) $key,
                    'label' => self::networkLabels()[$key] ?? Str::of((string) $key)->headline()->toString(),
                    'url' => $url,
                    'icon' => (string) $key,
                ]);
            })
            ->filter()
            ->values()
            ->all();
    }

    /** @return array{key: string, label: string, url: string, icon: string}|null */
    private function normalizeEntry(array $entry): ?array
    {
        $url = $this->publicUrl->sanitize($entry['url'] ?? null);

        if ($url === null) {
            return null;
        }

        $key = trim((string) ($entry['key'] ?? ''));
        $icon = trim((string) ($entry['icon'] ?? $key));
        $label = trim((string) ($entry['label'] ?? ''));

        if ($key === '') {
            $key = $icon !== '' ? $icon : Str::of($label)->slug('_')->toString();
        }

        if ($icon === '') {
            $icon = $key;
        }

        if ($label === '') {
            $label = self::networkLabels()[$icon] ?? self::networkLabels()[$key] ?? __('Društvena mreža');
        }

        return [
            'key' => $key,
            'label' => $label,
            'url' => $url,
            'icon' => $icon,
        ];
    }
}
