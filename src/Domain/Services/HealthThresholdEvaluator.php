<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Domain\Services;

use Hamzi\CoreWatch\Domain\Enums\AlertSeverity;
use Hamzi\CoreWatch\Domain\ValueObjects\Alert;

final class HealthThresholdEvaluator
{
    /**
     * @param  array<string, mixed>  $cpu
     * @param  array<string, mixed>  $ram
     * @param  array<string, mixed>  $disk
     * @return array<string, Alert>
     */
    public function evaluate(
        array $cpu,
        array $ram,
        array $disk,
        float $cpuThreshold,
        float $ramThreshold,
        float $diskThreshold,
    ): array {
        $alerts = [];

        if (($cpu['usage_percentage'] ?? 0) >= $cpuThreshold) {
            $alerts['cpu'] = new Alert(
                key: 'cpu',
                name: 'CPU Load Utilization',
                current: $cpu['usage_percentage'].'%',
                threshold: $cpuThreshold.'%',
                severity: AlertSeverity::Critical,
                details: "1-Min Load: {$cpu['load_1']}, Cores: {$cpu['cores']}",
            );
        }

        if (($ram['usage_percentage'] ?? 0) >= $ramThreshold) {
            $alerts['ram'] = new Alert(
                key: 'ram',
                name: 'RAM Allocation Limit',
                current: $ram['usage_percentage'].'%',
                threshold: $ramThreshold.'%',
                severity: AlertSeverity::Critical,
                details: "Used: {$ram['used_formatted']} / Total: {$ram['total_formatted']}",
            );
        }

        if (($disk['usage_percentage'] ?? 0) >= $diskThreshold) {
            $alerts['disk'] = new Alert(
                key: 'disk',
                name: 'Disk Space Saturation',
                current: $disk['usage_percentage'].'%',
                threshold: $diskThreshold.'%',
                severity: AlertSeverity::Warning,
                details: "Used: {$disk['used_formatted']} / Total: {$disk['total_formatted']} (Path: {$disk['path']})",
            );
        }

        return $alerts;
    }
}
