<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Repositories;

use Exception;
use Hamzi\CoreWatch\Contracts\DatabaseStatsRepositoryInterface;
use Hamzi\CoreWatch\Support\ByteFormatter;
use Illuminate\Support\Facades\DB;

final class DatabaseStatsRepository implements DatabaseStatsRepositoryInterface
{
    public function getStats(): array
    {
        try {
            $connection = DB::connection();
            $driver = $connection->getDriverName();
            $tablesCount = 0;
            $sizeBytes = 0;

            if ($driver === 'mysql') {
                $tablesResult = DB::select('SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = DATABASE()');
                $tablesCount = (int) ($tablesResult[0]->count ?? 0);

                $sizeResult = DB::select('SELECT SUM(data_length + index_length) AS size FROM information_schema.tables WHERE table_schema = DATABASE()');
                $sizeBytes = (int) ($sizeResult[0]->size ?? 0);
            } elseif ($driver === 'sqlite') {
                $tablesResult = DB::select("SELECT COUNT(*) as count FROM sqlite_master WHERE type='table'");
                $tablesCount = (int) ($tablesResult[0]->count ?? 0);

                $sizeResult = DB::select('SELECT page_count * page_size AS size FROM pragma_page_count(), pragma_page_size()');
                $sizeBytes = (int) ($sizeResult[0]->size ?? 0);
            } elseif ($driver === 'pgsql') {
                $tablesResult = DB::select("SELECT COUNT(*) as count FROM pg_catalog.pg_tables WHERE schemaname = 'public'");
                $tablesCount = (int) ($tablesResult[0]->count ?? 0);

                $sizeResult = DB::select('SELECT pg_database_size(current_database()) AS size');
                $sizeBytes = (int) ($sizeResult[0]->size ?? 0);
            }

            return [
                'driver' => strtoupper($driver),
                'tables_count' => $tablesCount,
                'size_formatted' => ByteFormatter::format($sizeBytes),
                'connection' => 'Connected ✅',
                'active' => true,
            ];
        } catch (Exception) {
            return [
                'driver' => 'Unknown',
                'tables_count' => 0,
                'size_formatted' => '0 B',
                'connection' => 'Disconnected ❌',
                'active' => false,
            ];
        }
    }
}
