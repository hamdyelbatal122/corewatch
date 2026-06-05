<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Repositories;

use Exception;
use Hamzi\CoreWatch\Contracts\ApplicationHealthRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final class ApplicationHealthRepository implements ApplicationHealthRepositoryInterface
{
    public function getChecks(): array
    {
        $checks = [];

        try {
            $cacheKey = 'corewatch_health_ping';
            Cache::put($cacheKey, true, 5);
            $cacheActive = Cache::get($cacheKey) === true;
            $checks['cache'] = [
                'name' => 'Cache System Driver',
                'status' => $cacheActive ? 'Operational ✅' : 'Failed ❌',
                'active' => $cacheActive,
                'detail' => 'Store driver: '.config('cache.default', 'unknown'),
            ];
        } catch (Exception $e) {
            $checks['cache'] = [
                'name' => 'Cache System Driver',
                'status' => 'Broken ❌',
                'active' => false,
                'detail' => $e->getMessage(),
            ];
        }

        try {
            $queueConnection = config('queue.default', 'sync');
            $checks['queue'] = [
                'name' => 'Artisan Queue Driver',
                'status' => 'Configured ✅',
                'active' => true,
                'detail' => 'Driver connection: '.$queueConnection,
            ];
        } catch (Exception $e) {
            $checks['queue'] = [
                'name' => 'Artisan Queue Driver',
                'status' => 'Unconfigured ⚠️',
                'active' => false,
                'detail' => $e->getMessage(),
            ];
        }

        $debugMode = (bool) config('app.debug', false);
        $checks['security'] = [
            'name' => 'Debug Diagnostics Mode',
            'status' => $debugMode ? 'Exposed ⚠️' : 'Secured ✅',
            'active' => ! $debugMode,
            'detail' => $debugMode ? 'Disable APP_DEBUG in production env.' : 'Direct public access exposures are closed.',
        ];

        $checks['environment'] = [
            'name' => 'Active Environment',
            'status' => app()->environment() === 'production' ? 'Production Mode 🚀' : 'Development / Staging 🛠️',
            'active' => true,
            'detail' => 'Current env: '.app()->environment(),
        ];

        return $checks;
    }
}
