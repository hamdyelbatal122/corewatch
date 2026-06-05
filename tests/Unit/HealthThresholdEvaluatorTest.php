<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Domain\Enums\AlertSeverity;
use Hamzi\CoreWatch\Domain\Services\HealthThresholdEvaluator;
use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class HealthThresholdEvaluatorTest extends TestCase
{
    private HealthThresholdEvaluator $evaluator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluator = new HealthThresholdEvaluator;
    }

    #[Test]
    public function it_returns_no_alerts_when_all_metrics_are_within_thresholds(): void
    {
        $alerts = $this->evaluator->evaluate(
            cpu: ['usage_percentage' => 50.0, 'load_1' => 1.0, 'cores' => 4],
            ram: ['usage_percentage' => 60.0, 'used_formatted' => '6 GB', 'total_formatted' => '10 GB'],
            disk: ['usage_percentage' => 70.0, 'used_formatted' => '70 GB', 'total_formatted' => '100 GB', 'path' => '/var/www'],
            cpuThreshold: 85.0,
            ramThreshold: 90.0,
            diskThreshold: 90.0,
        );

        $this->assertEmpty($alerts);
    }

    #[Test]
    public function it_triggers_cpu_alert_when_threshold_is_breached(): void
    {
        $alerts = $this->evaluator->evaluate(
            cpu: ['usage_percentage' => 95.0, 'load_1' => 3.8, 'cores' => 4],
            ram: ['usage_percentage' => 60.0, 'used_formatted' => '6 GB', 'total_formatted' => '10 GB'],
            disk: ['usage_percentage' => 70.0, 'used_formatted' => '70 GB', 'total_formatted' => '100 GB', 'path' => '/var/www'],
            cpuThreshold: 85.0,
            ramThreshold: 90.0,
            diskThreshold: 90.0,
        );

        $this->assertArrayHasKey('cpu', $alerts);
        $this->assertSame(AlertSeverity::Critical, $alerts['cpu']->severity);
        $this->assertSame('95%', $alerts['cpu']->current);
    }

    #[Test]
    public function it_triggers_disk_alert_with_warning_severity(): void
    {
        $alerts = $this->evaluator->evaluate(
            cpu: ['usage_percentage' => 50.0, 'load_1' => 1.0, 'cores' => 4],
            ram: ['usage_percentage' => 60.0, 'used_formatted' => '6 GB', 'total_formatted' => '10 GB'],
            disk: ['usage_percentage' => 95.0, 'used_formatted' => '95 GB', 'total_formatted' => '100 GB', 'path' => '/var/www'],
            cpuThreshold: 85.0,
            ramThreshold: 90.0,
            diskThreshold: 90.0,
        );

        $this->assertArrayHasKey('disk', $alerts);
        $this->assertSame(AlertSeverity::Warning, $alerts['disk']->severity);
    }
}
