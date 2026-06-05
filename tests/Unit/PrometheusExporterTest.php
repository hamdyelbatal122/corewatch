<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Infrastructure\Exporters\PrometheusExporter;
use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PrometheusExporterTest extends TestCase
{
    #[Test]
    public function it_exports_prometheus_format(): void
    {
        $exporter = new PrometheusExporter;

        $output = $exporter->export([
            'cpu' => ['usage_percentage' => 42.5, 'cores' => 4],
            'ram' => ['usage_percentage' => 60.0],
            'disk' => ['usage_percentage' => 75.0],
            'failed_jobs' => ['count' => 3],
            'ssl' => ['days_left' => 30],
        ]);

        $this->assertStringContainsString('corewatch_cpu_usage_percentage 42.5', $output);
        $this->assertStringContainsString('corewatch_failed_jobs_total 3', $output);
        $this->assertStringContainsString('corewatch_up 1', $output);
    }
}
