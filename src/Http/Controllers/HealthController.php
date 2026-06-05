<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Controllers;

use Hamzi\CoreWatch\CoreWatchManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class HealthController extends Controller
{
    public function __invoke(CoreWatchManager $coreWatch): JsonResponse
    {
        $health = $coreWatch->health();

        return response()->json($health, $health['healthy'] ? 200 : 503);
    }
}
