<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;
use Hamzi\CoreWatch\CoreWatchManager;
use Hamzi\CoreWatch\Facades\CoreWatch;
use Hamzi\CoreWatch\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

final class CoreWatchManagerTest extends TestCase
{
    #[Test]
    public function it_calculates_healthy_status_when_metrics_are_under_thresholds(): void
    {
        $collector = Mockery::mock(SystemMetricsCollectorInterface::class);
        $collector->shouldReceive('collectCpu')->andReturn(['usage_percentage' => 45.0]);
        $collector->shouldReceive('collectRam')->andReturn(['usage_percentage' => 60.0]);
        $collector->shouldReceive('collectDisk')->andReturn(['usage_percentage' => 70.0]);

        $this->app->instance(SystemMetricsCollectorInterface::class, $collector);

        $manager = $this->app->make(CoreWatchManager::class);
        $health = $manager->health();

        $this->assertTrue($health['healthy']);
        $this->assertSame('healthy', $health['status']);
        $this->assertTrue($health['checks']['cpu']['healthy']);
        $this->assertTrue($health['checks']['ram']['healthy']);
        $this->assertTrue($health['checks']['disk']['healthy']);
    }

    #[Test]
    public function it_calculates_degraded_status_when_any_metric_exceeds_threshold(): void
    {
        $collector = Mockery::mock(SystemMetricsCollectorInterface::class);
        $collector->shouldReceive('collectCpu')->andReturn(['usage_percentage' => 95.0]);
        $collector->shouldReceive('collectRam')->andReturn(['usage_percentage' => 50.0]);
        $collector->shouldReceive('collectDisk')->andReturn(['usage_percentage' => 60.0]);

        $this->app->instance(SystemMetricsCollectorInterface::class, $collector);

        $manager = $this->app->make(CoreWatchManager::class);
        $health = $manager->health();

        $this->assertFalse($health['healthy']);
        $this->assertSame('degraded', $health['status']);
        $this->assertFalse($health['checks']['cpu']['healthy']);
    }

    #[Test]
    public function it_forwards_doctor_call_via_manager_and_facade(): void
    {
        $manager = $this->app->make(CoreWatchManager::class);
        $doctorResults = $manager->doctor();

        $this->assertIsArray($doctorResults);
        $this->assertNotEmpty($doctorResults);

        $facadeResults = CoreWatch::doctor();
        $this->assertIsArray($facadeResults);
        $this->assertCount(count($doctorResults), $facadeResults);
    }
}
