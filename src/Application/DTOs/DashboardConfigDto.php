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
        return [
            'refresh_interval' => config('corewatch.refresh_interval', 5000),
            'widgets' => config('corewatch.widgets', []),
            'services' => array_map(
                fn ($key, $service) => ['key' => $key, 'name' => $service['name']],
                array_keys(config('corewatch.services', [])),
                config('corewatch.services', [])
            ),
            'logs' => array_map(
                fn ($key, $log) => ['key' => $key, 'name' => $log['name']],
                array_keys(config('corewatch.logs.files', [])),
                config('corewatch.logs.files', [])
            ),
        ];
    }
}
