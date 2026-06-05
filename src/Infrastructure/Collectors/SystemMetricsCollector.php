<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ApplicationHealthRepositoryInterface;
use Hamzi\CoreWatch\Contracts\DatabaseStatsRepositoryInterface;
use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;

final class SystemMetricsCollector implements SystemMetricsCollectorInterface
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
        private readonly CpuMetricsCollector $cpu,
        private readonly RamMetricsCollector $ram,
        private readonly DiskMetricsCollector $disk,
        private readonly UptimeCollector $uptime,
        private readonly SystemInfoCollector $systemInfo,
        private readonly ServicesStatusCollector $services,
        private readonly ProcessCollector $processes,
        private readonly DatabaseStatsRepositoryInterface $database,
        private readonly ApplicationHealthRepositoryInterface $applicationHealth,
    ) {}

    public function collect(): array
    {
        return [
            'cpu' => $this->collectCpu(),
            'ram' => $this->collectRam(),
            'disk' => $this->collectDisk(),
            'uptime' => $this->uptime->collect(),
            'system_info' => $this->collectSystemInfo(),
            'services' => $this->services->collect(),
            'processes' => $this->processes->collect(),
            'database' => $this->database->getStats(),
            'app_checks' => $this->applicationHealth->getChecks(),
        ];
    }

    public function collectCpu(): array
    {
        return $this->cpu->collect();
    }

    public function collectRam(): array
    {
        return $this->ram->collect();
    }

    public function collectDisk(): array
    {
        return $this->disk->collect();
    }

    public function collectSystemInfo(): array
    {
        return $this->systemInfo->collect();
    }

    public function isShellDisabled(): bool
    {
        return $this->shell->isDisabled();
    }
}
