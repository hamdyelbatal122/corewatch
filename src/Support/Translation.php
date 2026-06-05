<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Support;

final class Translation
{
    /**
     * @var array<string, string>
     */
    private const FALLBACKS = [
        'title' => 'COREWATCH',
        'subtitle' => 'Stealthy DevOps & Real-time Server Health Monitor',
        'polling_active' => 'Polling Active',
        'polling_suspended' => 'Polling Suspended',
        'uptime' => 'UPTIME',
        'refresh' => 'RE-POLL',
        'ops_insights' => 'Operations Insights',
        'ssl_certificate' => 'SSL Certificate',
        'failed_jobs' => 'Failed Queue Jobs',
        'scheduler' => 'Task Scheduler',
        'service_disabled' => 'This service command is disabled by configuration.',
        'table_count' => 'Table Count',
        'workspace_path' => 'Workspace Path',
        'secure_triggers' => 'Secure Whitelisted Triggers',
        'loading' => 'Loading...',
    ];

    public static function get(string $key): string
    {
        $fullKey = "corewatch::{$key}";
        $translated = trans($fullKey);

        if ($translated !== $fullKey) {
            return $translated;
        }

        return self::FALLBACKS[$key] ?? str_replace('_', ' ', ucfirst($key));
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        $labels = [];
        foreach (array_keys(self::FALLBACKS) as $key) {
            $labels[$key] = self::get($key);
        }

        return $labels;
    }
}
