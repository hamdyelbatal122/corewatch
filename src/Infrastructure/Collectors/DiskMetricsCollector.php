<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Exception;
use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Support\ByteFormatter;

final class DiskMetricsCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        $path = base_path();

        try {
            $total = (float) @disk_total_space($path);
            $free = (float) @disk_free_space($path);

            if ($total <= 0) {
                throw new Exception('Native disk metrics returned zero or are restricted.');
            }
        } catch (Exception) {
            $total = 1.0;
            $free = 1.0;
            $result = $this->shell->run('df -P '.escapeshellarg($path));
            if ($result['success']) {
                $lines = explode("\n", trim($result['output']));
                if (count($lines) >= 2) {
                    $parts = preg_split('/\s+/', $lines[1]);
                    if (count($parts) >= 6) {
                        $total = (float) $parts[1] * 1024;
                        $free = (float) $parts[3] * 1024;
                    }
                }
            }
        }

        $used = $total - $free;
        $usagePercentage = round(($used / $total) * 100, 2);

        return [
            'total' => $total,
            'total_formatted' => ByteFormatter::format((int) $total),
            'used' => $used,
            'used_formatted' => ByteFormatter::format((int) $used),
            'free' => $free,
            'free_formatted' => ByteFormatter::format((int) $free),
            'usage_percentage' => min(100.0, max(0.0, $usagePercentage)),
            'path' => $path,
        ];
    }
}
