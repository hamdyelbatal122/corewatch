<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Domain\Services;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class SystemDoctor
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<int, array{category: string, check: string, status: string, detail: string}>
     */
    public function runDiagnostics(): array
    {
        $checks = [];

        // 1. PHP Version
        $phpOk = version_compare(PHP_VERSION, '8.2.0', '>=');
        $checks[] = [
            'category' => 'System',
            'check' => 'PHP Version',
            'status' => $phpOk ? 'PASS' : 'FAIL',
            'detail' => 'Running PHP '.PHP_VERSION.($phpOk ? ' (supported)' : ' (requires >= 8.2)'),
        ];

        // 2. OpenSSL Extension
        $hasOpenSsl = extension_loaded('openssl');
        $checks[] = [
            'category' => 'System',
            'check' => 'OpenSSL Extension',
            'status' => $hasOpenSsl ? 'PASS' : 'WARN',
            'detail' => $hasOpenSsl ? 'Enabled (required for SSL certificate monitoring)' : 'Missing openssl extension',
        ];

        // 3. Proc Filesystem
        $procLoadReadable = is_readable('/proc/loadavg');
        $procMemReadable = is_readable('/proc/meminfo');
        $procUptimeReadable = is_readable('/proc/uptime');
        $procAll = $procLoadReadable && $procMemReadable && $procUptimeReadable;
        $checks[] = [
            'category' => 'System',
            'check' => '/proc Access',
            'status' => $procAll ? 'PASS' : 'WARN',
            'detail' => $procAll
                ? 'Direct /proc filesystem metrics available'
                : 'Limited access (CoreWatch will use shell fallbacks)',
        ];

        // 4. Shell Executor
        $shellDisabled = $this->shell->isDisabled();
        $checks[] = [
            'category' => 'System',
            'check' => 'Shell Execution',
            'status' => ! $shellDisabled ? 'PASS' : 'WARN',
            'detail' => ! $shellDisabled
                ? 'proc_open/exec functions available'
                : 'Shell functions disabled in php.ini (native /proc fallbacks active)',
        ];

        // 5. Database Connection
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $durationMs = round((microtime(true) - $start) * 1000, 2);
            $checks[] = [
                'category' => 'Database',
                'check' => 'Connection & Ping',
                'status' => 'PASS',
                'detail' => 'Connected to '.strtoupper(DB::connection()->getDriverName())." ({$durationMs} ms)",
            ];
        } catch (Throwable $e) {
            $checks[] = [
                'category' => 'Database',
                'check' => 'Connection & Ping',
                'status' => 'FAIL',
                'detail' => 'Connection failed: '.$e->getMessage(),
            ];
        }

        // 6. Cache System
        try {
            $testKey = 'corewatch_doctor_probe_'.uniqid();
            Cache::put($testKey, 'ok', 10);
            $cached = Cache::get($testKey);
            Cache::forget($testKey);

            $cacheOk = ($cached === 'ok');
            $checks[] = [
                'category' => 'Cache',
                'check' => 'Read & Write Probe',
                'status' => $cacheOk ? 'PASS' : 'FAIL',
                'detail' => 'Driver: '.config('cache.default', 'unknown'),
            ];
        } catch (Throwable $e) {
            $checks[] = [
                'category' => 'Cache',
                'check' => 'Read & Write Probe',
                'status' => 'FAIL',
                'detail' => 'Cache error: '.$e->getMessage(),
            ];
        }

        // 7. Queue & Failed Jobs
        $queueDriver = config('queue.default', 'sync');
        $checks[] = [
            'category' => 'Queue',
            'check' => 'Default Driver',
            'status' => 'PASS',
            'detail' => 'Connection: '.$queueDriver,
        ];

        $hasFailedJobs = Schema::hasTable('failed_jobs');
        $checks[] = [
            'category' => 'Queue',
            'check' => 'Failed Jobs Table',
            'status' => $hasFailedJobs ? 'PASS' : 'WARN',
            'detail' => $hasFailedJobs ? 'Table exists and monitored' : 'Table missing (run artisan queue:failed-table)',
        ];

        // 8. Scheduler Heartbeat
        $heartbeatKey = config('corewatch.schedule.heartbeat_cache_key', 'corewatch_schedule_heartbeat');
        $heartbeat = Cache::get($heartbeatKey);
        $checks[] = [
            'category' => 'Scheduler',
            'check' => 'Heartbeat Signal',
            'status' => $heartbeat !== null ? 'PASS' : 'WARN',
            'detail' => $heartbeat !== null
                ? 'Active (last recorded: '.$heartbeat.')'
                : 'No heartbeat recorded (schedule corewatch:heartbeat in routes/console.php)',
        ];

        // 9. Alert Channels
        /** @var array<int, string> $channels */
        $channels = config('corewatch.notifications.channels', []);
        $slackSet = ! empty(config('corewatch.notifications.slack.webhook_url'));
        $telegramSet = ! empty(config('corewatch.notifications.telegram.bot_token'))
            && ! empty(config('corewatch.notifications.telegram.chat_id'));
        $queueAlerts = config('corewatch.notifications.queue', false);

        $channelsDetail = [];
        if (in_array('slack', $channels, true)) {
            $channelsDetail[] = $slackSet ? 'Slack configured' : 'Slack missing webhook URL';
        }
        if (in_array('telegram', $channels, true)) {
            $channelsDetail[] = $telegramSet ? 'Telegram configured' : 'Telegram missing credentials';
        }
        if (count($channelsDetail) === 0) {
            $channelsDetail[] = 'No alert channels registered';
        }

        $checks[] = [
            'category' => 'Alerts',
            'check' => 'Notification Channels',
            'status' => ($slackSet || $telegramSet) ? 'PASS' : 'WARN',
            'detail' => implode(', ', $channelsDetail).' | Queue mode: '.($queueAlerts !== false ? 'ENABLED' : 'SYNC'),
        ];

        // 10. Security
        $debug = (bool) config('app.debug', false);
        $isProduction = app()->environment('production');
        $checks[] = [
            'category' => 'Security',
            'check' => 'Debug Mode',
            'status' => ($isProduction && $debug) ? 'FAIL' : 'PASS',
            'detail' => $debug ? 'APP_DEBUG is enabled' : 'APP_DEBUG is disabled',
        ];

        $gate = config('corewatch.gate');
        $hasGate = $gate !== null && is_callable($gate);
        $checks[] = [
            'category' => 'Security',
            'check' => 'Authorization Gate',
            'status' => $hasGate ? 'PASS' : ($isProduction ? 'WARN' : 'PASS'),
            'detail' => $hasGate
                ? 'Custom authorization gate callback configured'
                : 'Default middleware used (define corewatch.gate for fine-grained control)',
        ];

        return $checks;
    }
}
