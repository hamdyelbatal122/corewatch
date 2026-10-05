<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\DTOs;

use Hamzi\CoreWatch\Support\PackageVersion;
use Hamzi\CoreWatch\Support\Translation;

final readonly class DashboardConfigDto
{
    /**
     * @return array<string, mixed>
     */
    public static function fromConfig(): array
    {
        /** @var array<string, array{name: string, command: string, type: string, enabled?: bool}> $configuredServices */
        $configuredServices = config('corewatch.services', []);

        $services = [];
        foreach ($configuredServices as $key => $service) {
            if (($service['enabled'] ?? true) === true) {
                $services[] = [
                    'key' => $key,
                    'name' => $service['name'],
                ];
            }
        }

        /** @var array<string, array{name: string, path: string, type: string}> $configuredLogs */
        $configuredLogs = config('corewatch.logs.files', []);

        $logs = [];
        foreach ($configuredLogs as $key => $log) {
            $logs[] = [
                'key' => $key,
                'name' => $log['name'],
            ];
        }

        return [
            'refresh_interval' => config('corewatch.refresh_interval', 5000),
            'widgets' => config('corewatch.widgets', []),
            'services' => $services,
            'logs' => $logs,
            'locale' => app()->getLocale(),
            'labels' => Translation::all(),
            'version' => PackageVersion::current(),
        ];
    }
}
