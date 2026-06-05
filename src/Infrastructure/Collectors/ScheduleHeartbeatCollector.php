<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

final class ScheduleHeartbeatCollector
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        if (! config('corewatch.schedule.enabled', true)) {
            return [
                'enabled' => false,
                'status' => 'Disabled',
                'active' => true,
                'detail' => 'Schedule monitoring is disabled in config',
            ];
        }

        $cacheKey = config('corewatch.schedule.heartbeat_cache_key', 'corewatch_schedule_heartbeat');
        $maxAgeMinutes = (int) config('corewatch.schedule.max_age_minutes', 10);
        $lastBeat = Cache::get($cacheKey);

        if ($lastBeat === null) {
            return [
                'enabled' => true,
                'status' => 'No Heartbeat ⚠️',
                'active' => false,
                'detail' => 'Add Schedule::command(\'corewatch:heartbeat\')->everyMinute() to routes/console.php',
            ];
        }

        $lastBeatAt = Carbon::parse($lastBeat);
        $minutesAgo = (int) $lastBeatAt->diffInMinutes(now());
        $active = $minutesAgo <= $maxAgeMinutes;

        return [
            'enabled' => true,
            'last_heartbeat' => $lastBeatAt->toIso8601String(),
            'minutes_ago' => $minutesAgo,
            'status' => $active ? 'Scheduler Active ✅' : 'Scheduler Stale ❌',
            'active' => $active,
            'detail' => $active
                ? "Last heartbeat {$minutesAgo} minute(s) ago"
                : "No heartbeat within {$maxAgeMinutes} minutes — is cron running?",
        ];
    }
}
