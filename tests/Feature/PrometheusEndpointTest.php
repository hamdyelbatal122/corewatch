<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Feature;

use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class PrometheusEndpointTest extends TestCase
{
    #[Test]
    public function test_prometheus_endpoint_returns_plain_text_metrics(): void
    {
        $response = $this->get('/corewatch/api/metrics/prometheus');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; version=0.0.4; charset=utf-8');
        $this->assertStringContainsString('corewatch_cpu_usage_percentage', $response->getContent());
        $this->assertStringContainsString('corewatch_up 1', $response->getContent());
    }
}
