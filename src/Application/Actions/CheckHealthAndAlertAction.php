<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\Actions;

use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;
use Hamzi\CoreWatch\Domain\Services\HealthThresholdEvaluator;
use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Hamzi\CoreWatch\Events\ThresholdBreached;
use Hamzi\CoreWatch\Infrastructure\Notifications\AlertDispatcher;
use Illuminate\Support\Facades\Event;

final class CheckHealthAndAlertAction
{
    public function __construct(
        private readonly SystemMetricsCollectorInterface $collector,
        private readonly HealthThresholdEvaluator $evaluator,
        private readonly AlertDispatcher $alertDispatcher,
    ) {}

    /**
     * @return array{
     *     alerts: array<string, Alert>,
     *     cpu: array<string, mixed>,
     *     ram: array<string, mixed>,
     *     disk: array<string, mixed>,
     *     system_info: array<string, string>
     * }
     */
    public function execute(): array
    {
        $cpu = $this->collector->collectCpu();
        $ram = $this->collector->collectRam();
        $disk = $this->collector->collectDisk();
        $systemInfo = $this->collector->collectSystemInfo();

        $cpuThreshold = (float) config('corewatch.thresholds.cpu', 85.0);
        $ramThreshold = (float) config('corewatch.thresholds.ram', 90.0);
        $diskThreshold = (float) config('corewatch.thresholds.disk', 90.0);

        $alerts = $this->evaluator->evaluate(
            $cpu,
            $ram,
            $disk,
            $cpuThreshold,
            $ramThreshold,
            $diskThreshold,
        );

        if (count($alerts) > 0) {
            Event::dispatch(new ThresholdBreached($alerts, $systemInfo));
            $this->alertDispatcher->dispatch($alerts, $systemInfo);
        }

        return [
            'alerts' => $alerts,
            'cpu' => $cpu,
            'ram' => $ram,
            'disk' => $disk,
            'system_info' => $systemInfo,
            'thresholds' => [
                'cpu' => $cpuThreshold,
                'ram' => $ramThreshold,
                'disk' => $diskThreshold,
            ],
        ];
    }
}
