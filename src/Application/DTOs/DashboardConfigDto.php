<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\DTOs;

final readonly class DashboardConfigDto
{
    /**
     * @return array<string, mixed>
     */
    public static function fromConfig(): array
    {
        $services = collect(config('corewatch.services', []))
            ->filter(fn (array $service) => ($service['enabled'] ?? true) === true)
            ->map(fn (array $service, string $key) => ['key' => $key, 'name' => $service['name']])
            ->values()
            ->all();

        return [
            'refresh_interval' => config('corewatch.refresh_interval', 5000),
            'widgets' => config('corewatch.widgets', []),
            'services' => $services,
            'logs' => array_map(
                fn ($key, $log) => ['key' => $key, 'name' => $log['name']],
                array_keys(config('corewatch.logs.files', [])),
                config('corewatch.logs.files', [])
            ),
            'locale' => app()->getLocale(),
        ];
    }
}
