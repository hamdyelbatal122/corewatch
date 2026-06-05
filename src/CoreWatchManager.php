<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch;

use Hamzi\CoreWatch\Application\Actions\CheckHealthAndAlertAction;
use Hamzi\CoreWatch\Application\Actions\ExecuteServiceCommandAction;
use Hamzi\CoreWatch\Application\Actions\GetServerMetricsAction;
use Hamzi\CoreWatch\Application\Actions\ParseLogFileAction;
use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;
use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;

/**
 * Central entry point for programmatic CoreWatch access.
 *
 * Use via the CoreWatch facade or dependency injection in your application code.
 */
final class CoreWatchManager
{
    public function __construct(
        private readonly GetServerMetricsAction $metrics,
        private readonly ParseLogFileAction $logs,
        private readonly ExecuteServiceCommandAction $services,
        private readonly CheckHealthAndAlertAction $healthCheck,
        private readonly SystemMetricsCollectorInterface $collector,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function metrics(): array
    {
        return $this->metrics->execute();
    }

    /**
     * @return array<string, mixed>
     */
    public function cpu(): array
    {
        return $this->collector->collectCpu();
    }

    /**
     * @return array<string, mixed>
     */
    public function ram(): array
    {
        return $this->collector->collectRam();
    }

    /**
     * @return array<string, mixed>
     */
    public function disk(): array
    {
        return $this->collector->collectDisk();
    }

    /**
     * Lightweight health snapshot for uptime monitors and load balancers.
     *
     * @return array{status: string, healthy: bool, checks: array<string, mixed>, timestamp: string}
     */
    public function health(): array
    {
        $cpu = $this->collector->collectCpu();
        $ram = $this->collector->collectRam();
        $disk = $this->collector->collectDisk();

        $cpuThreshold = (float) config('corewatch.thresholds.cpu', 85.0);
        $ramThreshold = (float) config('corewatch.thresholds.ram', 90.0);
        $diskThreshold = (float) config('corewatch.thresholds.disk', 90.0);

        $checks = [
            'cpu' => [
                'usage' => $cpu['usage_percentage'],
                'threshold' => $cpuThreshold,
                'healthy' => $cpu['usage_percentage'] < $cpuThreshold,
            ],
            'ram' => [
                'usage' => $ram['usage_percentage'],
                'threshold' => $ramThreshold,
                'healthy' => $ram['usage_percentage'] < $ramThreshold,
            ],
            'disk' => [
                'usage' => $disk['usage_percentage'],
                'threshold' => $diskThreshold,
                'healthy' => $disk['usage_percentage'] < $diskThreshold,
            ],
        ];

        $healthy = collect($checks)->every(fn (array $check) => $check['healthy']);

        return [
            'status' => $healthy ? 'healthy' : 'degraded',
            'healthy' => $healthy,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array{logs: array<int, array<string, mixed>>, total_scanned: int, has_more: bool, error?: string}
     */
    public function readLogs(string $fileKey, int $page = 1, ?LogFilterDto $filters = null): array
    {
        return $this->logs->execute($fileKey, $page, $filters ?? new LogFilterDto);
    }

    /**
     * @return array{success: bool, service?: string, output?: string, error?: string}
     */
    public function runService(string $serviceKey): array
    {
        return $this->services->execute($serviceKey);
    }

    /**
     * @return array<string, mixed>
     */
    public function checkHealth(): array
    {
        return $this->healthCheck->execute();
    }
}
