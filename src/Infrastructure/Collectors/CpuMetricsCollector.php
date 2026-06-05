<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;

final class CpuMetricsCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        $cores = $this->getCoresCount();
        $loadAvg = $this->getLoadAverage();

        $loadPercentage = $cores > 0 ? ($loadAvg[0] / $cores) * 100 : 0.0;
        $loadPercentage = min(100.0, round($loadPercentage, 2));

        return [
            'cores' => $cores,
            'load_1' => $loadAvg[0],
            'load_5' => $loadAvg[1],
            'load_15' => $loadAvg[2],
            'usage_percentage' => $loadPercentage,
        ];
    }

    /**
     * @return array{0: float, 1: float, 2: float}
     */
    private function getLoadAverage(): array
    {
        $loadAvg = [0.0, 0.0, 0.0];

        if (is_readable('/proc/loadavg')) {
            $loadStr = file_get_contents('/proc/loadavg');
            if ($loadStr !== false) {
                $parts = explode(' ', trim($loadStr));
                if (count($parts) >= 3) {
                    return [
                        (float) $parts[0],
                        (float) $parts[1],
                        (float) $parts[2],
                    ];
                }
            }
        }

        if (function_exists('sys_getloadavg')) {
            $sysLoad = sys_getloadavg();
            if (is_array($sysLoad) && count($sysLoad) >= 3) {
                return [
                    (float) $sysLoad[0],
                    (float) $sysLoad[1],
                    (float) $sysLoad[2],
                ];
            }
        }

        $result = $this->shell->run('uptime');
        if ($result['success'] && preg_match('/load average[s]?:/i', $result['output'])) {
            $parts = preg_split('/load average[s]?:/i', $result['output']);
            if (isset($parts[1])) {
                $loads = explode(',', $parts[1]);

                return [
                    (float) trim($loads[0] ?? '0'),
                    (float) trim($loads[1] ?? '0'),
                    (float) trim($loads[2] ?? '0'),
                ];
            }
        }

        return $loadAvg;
    }

    private function getCoresCount(): int
    {
        if (is_readable('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== false) {
                preg_match_all('/^processor/m', $cpuinfo, $matches);
                $count = count($matches[0]);
                if ($count > 0) {
                    return $count;
                }
            }
        }

        $result = $this->shell->run('nproc');
        if ($result['success']) {
            return (int) trim($result['output']);
        }

        $result = $this->shell->run('lscpu');
        if ($result['success'] && preg_match('/^CPU\(s\):\s+(\d+)/m', $result['output'], $matches)) {
            return (int) $matches[1];
        }

        return 1;
    }
}
