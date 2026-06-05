<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class FailedJobsRepository
{
    /**
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        if (! Schema::hasTable('failed_jobs')) {
            return [
                'available' => false,
                'count' => 0,
                'status' => 'N/A',
                'active' => true,
                'detail' => 'failed_jobs table not found (queue driver may be sync)',
            ];
        }

        $count = (int) DB::table('failed_jobs')->count();
        $active = $count === 0;

        return [
            'available' => true,
            'count' => $count,
            'status' => $active ? 'Clear ✅' : "{$count} Failed ⚠️",
            'active' => $active,
            'detail' => $active ? 'No failed queue jobs' : 'Review failed jobs in Horizon or artisan queue:failed',
        ];
    }
}
