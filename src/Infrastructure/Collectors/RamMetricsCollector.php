<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Support\ByteFormatter;

final class RamMetricsCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        $total = 0;
        $free = 0;
        $available = 0;

        if (is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            if ($meminfo !== false) {
                preg_match('/^MemTotal:\s+(\d+)\s+kB/m', $meminfo, $totalMatches);
                preg_match('/^MemFree:\s+(\d+)\s+kB/m', $meminfo, $freeMatches);
                preg_match('/^MemAvailable:\s+(\d+)\s+kB/m', $meminfo, $availMatches);

                $total = isset($totalMatches[1]) ? (int) $totalMatches[1] * 1024 : 0;
                $free = isset($freeMatches[1]) ? (int) $freeMatches[1] * 1024 : 0;

                if (isset($availMatches[1])) {
                    $available = (int) $availMatches[1] * 1024;
                } else {
                    preg_match('/^Buffers:\s+(\d+)\s+kB/m', $meminfo, $bufferMatches);
                    preg_match('/^Cached:\s+(\d+)\s+kB/m', $meminfo, $cacheMatches);
                    $buffers = isset($bufferMatches[1]) ? (int) $bufferMatches[1] * 1024 : 0;
                    $cached = isset($cacheMatches[1]) ? (int) $cacheMatches[1] * 1024 : 0;
                    $available = $free + $buffers + $cached;
                }
            }
        }

        if ($total === 0) {
            $result = $this->shell->run('free -b');
            if ($result['success']) {
                $lines = explode("\n", trim($result['output']));
                foreach ($lines as $line) {
                    if (str_starts_with(strtolower($line), 'mem:')) {
                        $parts = preg_split('/\s+/', $line);
                        $total = (int) ($parts[1] ?? 0);
                        $free = (int) ($parts[3] ?? 0);
                        $available = (int) ($parts[6] ?? ($free + ($parts[4] ?? 0) + ($parts[5] ?? 0)));
                        break;
                    }
                }
            }
        }

        if ($total === 0) {
            $total = 1;
            $available = 1;
        }

        $used = $total - $available;
        $usagePercentage = round(($used / $total) * 100, 2);

        return [
            'total' => $total,
            'total_formatted' => ByteFormatter::format($total),
            'used' => $used,
            'used_formatted' => ByteFormatter::format($used),
            'free' => $free,
            'free_formatted' => ByteFormatter::format($free),
            'available' => $available,
            'available_formatted' => ByteFormatter::format($available),
            'usage_percentage' => min(100.0, max(0.0, $usagePercentage)),
        ];
    }
}
