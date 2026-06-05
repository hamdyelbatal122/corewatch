<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Controllers;

use Hamzi\CoreWatch\Application\Actions\GetServerMetricsAction;
use Hamzi\CoreWatch\Infrastructure\Exporters\PrometheusExporter;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class PrometheusController extends Controller
{
    public function __invoke(
        GetServerMetricsAction $metrics,
        PrometheusExporter $exporter,
    ): Response {
        $body = $exporter->export($metrics->execute());

        return response($body, 200, [
            'Content-Type' => 'text/plain; version=0.0.4; charset=utf-8',
        ]);
    }
}
