<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Controllers;

use Exception;
use Hamzi\CoreWatch\Application\Actions\ExecuteServiceCommandAction;
use Hamzi\CoreWatch\Application\Actions\GetServerMetricsAction;
use Hamzi\CoreWatch\Application\Actions\ParseLogFileAction;
use Hamzi\CoreWatch\Application\DTOs\DashboardConfigDto;
use Hamzi\CoreWatch\Http\Requests\ControlServiceRequest;
use Hamzi\CoreWatch\Http\Requests\LogsRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

final class DashboardController extends Controller
{
    public function __construct(
        private readonly GetServerMetricsAction $getMetrics,
        private readonly ParseLogFileAction $parseLogs,
        private readonly ExecuteServiceCommandAction $executeService,
    ) {}

    public function index(): View|Response
    {
        $config = DashboardConfigDto::fromConfig();

        return view('corewatch::dashboard', compact('config'));
    }

    public function metrics(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'metrics' => $this->getMetrics->execute(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function logs(LogsRequest $request): JsonResponse
    {
        try {
            $parsedData = $this->parseLogs->execute(
                $request->input('file'),
                (int) $request->input('page', 1),
                $request->toFilterDto(),
            );

            if (! empty($parsedData['not_found'])) {
                return response()->json([
                    'success' => false,
                    'error' => $parsedData['error'],
                ], 404);
            }

            return response()->json([
                'success' => true,
                'logs' => $parsedData['logs'],
                'has_more' => $parsedData['has_more'],
                'total_scanned' => $parsedData['total_scanned'],
                'file_name' => $parsedData['file_name'],
                'file_path' => $parsedData['file_path'],
                'error' => $parsedData['error'] ?? null,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function controlService(ControlServiceRequest $request): JsonResponse
    {
        $result = $this->executeService->execute($request->input('service_key'));

        if (! empty($result['not_found'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 400);
        }

        if (! $result['success'] && isset($result['error'])) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
            ], 500);
        }

        return response()->json([
            'success' => $result['success'],
            'service' => $result['service'] ?? null,
            'output' => $result['output'] ?? null,
        ]);
    }
}
