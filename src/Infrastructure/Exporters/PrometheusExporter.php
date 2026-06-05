<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Exporters;

final class PrometheusExporter
{
    /**
     * @param  array<string, mixed>  $metrics
     */
    public function export(array $metrics): string
    {
        $lines = [];

        $this->appendGauge($lines, 'corewatch_cpu_usage_percentage', (float) ($metrics['cpu']['usage_percentage'] ?? 0));
        $this->appendGauge($lines, 'corewatch_cpu_cores', (float) ($metrics['cpu']['cores'] ?? 0));
        $this->appendGauge($lines, 'corewatch_ram_usage_percentage', (float) ($metrics['ram']['usage_percentage'] ?? 0));
        $this->appendGauge($lines, 'corewatch_disk_usage_percentage', (float) ($metrics['disk']['usage_percentage'] ?? 0));

        if (isset($metrics['failed_jobs']['count'])) {
            $this->appendGauge($lines, 'corewatch_failed_jobs_total', (float) $metrics['failed_jobs']['count']);
        }

        if (isset($metrics['ssl']['days_left'])) {
            $this->appendGauge($lines, 'corewatch_ssl_days_remaining', (float) $metrics['ssl']['days_left']);
        }

        $lines[] = '# HELP corewatch_up CoreWatch metrics collection status';
        $lines[] = '# TYPE corewatch_up gauge';
        $lines[] = 'corewatch_up 1';

        return implode("\n", $lines)."\n";
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function appendGauge(array &$lines, string $name, float $value): void
    {
        $lines[] = "# HELP {$name} CoreWatch metric {$name}";
        $lines[] = "# TYPE {$name} gauge";
        $lines[] = "{$name} {$value}";
    }
}
