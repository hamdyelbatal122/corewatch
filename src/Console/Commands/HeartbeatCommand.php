<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class HeartbeatCommand extends Command
{
    protected $signature = 'corewatch:heartbeat';

    protected $description = 'Record a scheduler heartbeat for CoreWatch cron monitoring';

    public function handle(): int
    {
        $cacheKey = config('corewatch.schedule.heartbeat_cache_key', 'corewatch_schedule_heartbeat');
        $ttlMinutes = (int) config('corewatch.schedule.heartbeat_ttl_minutes', 30);

        Cache::put($cacheKey, now()->toIso8601String(), now()->addMinutes($ttlMinutes));

        $this->components->info('CoreWatch scheduler heartbeat recorded.');

        return Command::SUCCESS;
    }
}
