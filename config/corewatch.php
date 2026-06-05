<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | CoreWatch Dashboard Activation
    |--------------------------------------------------------------------------
    |
    | Enable or disable the entire CoreWatch server monitoring suite.
    |
    */
    'enabled' => env('COREWATCH_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Dashboard URI Route Path
    |--------------------------------------------------------------------------
    |
    | The path where the CoreWatch dashboard will be accessible.
    |
    */
    'path' => env('COREWATCH_PATH', 'corewatch'),

    /*
    |--------------------------------------------------------------------------
    | Allowed Environments
    |--------------------------------------------------------------------------
    |
    | The environments in which CoreWatch is permitted to run.
    | Useful for restricting access in production if needed.
    |
    */
    'environments' => [
        'local',
        'staging',
        'production',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Define the middleware stack to protect the CoreWatch routes.
    | By default, we recommend utilizing 'web' and 'auth'. You can also
    | create your own custom authorization middleware.
    |
    */
    'middleware' => [
        'web',
        // 'auth', // Uncomment in production to require authentication
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Gate / Callback
    |--------------------------------------------------------------------------
    |
    | Set a callable in your AppServiceProvider for fine-grained access control:
    |
    |   config(['corewatch.gate' => fn ($request) => $request->user()?->isAdmin()]);
    |
    | Environment variables cannot hold callables; keep this null and configure
    | programmatically, or rely on the middleware stack above.
    |
    */
    'gate' => null,

    /*
    |--------------------------------------------------------------------------
    | UI Refresh Interval
    |--------------------------------------------------------------------------
    |
    | Asynchronous polling interval in milliseconds for the AlpineJS frontend
    | to fetch real-time server metrics from the internal API.
    |
    */
    'refresh_interval' => env('COREWATCH_REFRESH_INTERVAL', 5000),

    /*
    |--------------------------------------------------------------------------
    | Metrics Cache TTL (seconds)
    |--------------------------------------------------------------------------
    |
    | Cache collected metrics to reduce /proc reads. Set to 0 to disable.
    |
    */
    'metrics_cache_ttl' => (int) env('COREWATCH_METRICS_CACHE_TTL', 5),

    /*
    |--------------------------------------------------------------------------
    | API Rate Limiting (requests per minute)
    |--------------------------------------------------------------------------
    */
    'rate_limit' => (int) env('COREWATCH_RATE_LIMIT', 60),

    /*
    |--------------------------------------------------------------------------
    | Service Command Audit Log
    |--------------------------------------------------------------------------
    */
    'audit_log' => [
        'enabled' => env('COREWATCH_AUDIT_LOG', true),
        'channel' => env('COREWATCH_AUDIT_LOG_CHANNEL', 'single'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Health Check Endpoint
    |--------------------------------------------------------------------------
    |
    | Lightweight JSON endpoint at /corewatch/api/health for uptime monitors,
    | load balancers, and Kubernetes probes. Returns HTTP 200 when healthy,
    | HTTP 503 when resource thresholds are breached.
    |
    | Set public to true to expose without corewatch.auth middleware.
    |
    */
    'health_endpoint' => [
        'enabled' => env('COREWATCH_HEALTH_ENDPOINT', true),
        'public' => env('COREWATCH_HEALTH_PUBLIC', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prometheus Metrics Endpoint
    |--------------------------------------------------------------------------
    */
    'prometheus_endpoint' => [
        'enabled' => env('COREWATCH_PROMETHEUS_ENDPOINT', true),
        'public' => env('COREWATCH_PROMETHEUS_PUBLIC', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSL Certificate Monitoring
    |--------------------------------------------------------------------------
    */
    'ssl' => [
        'enabled' => env('COREWATCH_SSL_ENABLED', true),
        'host' => env('COREWATCH_SSL_HOST'),
        'port' => (int) env('COREWATCH_SSL_PORT', 443),
        'warning_days' => (int) env('COREWATCH_SSL_WARNING_DAYS', 14),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduler Heartbeat Monitoring
    |--------------------------------------------------------------------------
    |
    | Register: Schedule::command('corewatch:heartbeat')->everyMinute();
    |
    */
    'schedule' => [
        'enabled' => env('COREWATCH_SCHEDULE_MONITOR', true),
        'heartbeat_cache_key' => 'corewatch_schedule_heartbeat',
        'heartbeat_ttl_minutes' => 30,
        'max_age_minutes' => (int) env('COREWATCH_SCHEDULE_MAX_AGE', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Enabled Widgets & Modules
    |--------------------------------------------------------------------------
    |
    | Toggle individual widget panels on the dashboard UI.
    |
    */
    'widgets' => [
        'cpu' => true,
        'ram' => true,
        'disk' => true,
        'services' => true,
        'logs' => true,
        'ops_insights' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Alerting Thresholds
    |--------------------------------------------------------------------------
    |
    | Set the limits for hardware resources. The health cron job evaluates
    | these parameters and triggers alerts if they are breached.
    |
    */
    'thresholds' => [
        'cpu' => (float) env('COREWATCH_CPU_THRESHOLD', 85.0),  // in %
        'ram' => (float) env('COREWATCH_RAM_THRESHOLD', 90.0),  // in %
        'disk' => (float) env('COREWATCH_DISK_THRESHOLD', 90.0), // in %
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notification channels for alerts when resource thresholds are
    | exceeded. Supports Slack Webhooks and Telegram Bot API.
    |
    */
    'notifications' => [
        'channels' => ['slack', 'telegram'], // 'slack', 'telegram'

        'slack' => [
            'webhook_url' => env('COREWATCH_SLACK_WEBHOOK_URL'),
            'channel' => env('COREWATCH_SLACK_CHANNEL', '#devops-alerts'),
        ],

        'telegram' => [
            'bot_token' => env('COREWATCH_TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('COREWATCH_TELEGRAM_CHAT_ID'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Safe System Service Controls
    |--------------------------------------------------------------------------
    |
    | CoreWatch lets you execute specific pre-configured administrative commands
    | safely via the UI. Only commands defined here can be run.
    | To protect your server, DO NOT accept raw inputs from the request.
    |
    */
    'services' => [
        'php_queue' => [
            'name' => 'Artisan Queue Restart',
            'command' => 'queue:restart',
            'type' => 'artisan',
            'enabled' => true,
        ],
        'cache_clear' => [
            'name' => 'Clear Application Cache',
            'command' => 'cache:clear',
            'type' => 'artisan',
            'enabled' => true,
        ],
        'redis_flush' => [
            'name' => 'Flush Redis Cache',
            'command' => 'redis-cli flushall',
            'type' => 'shell',
            'enabled' => false,
        ],
        'supervisor_restart' => [
            'name' => 'Restart Supervisor Services',
            'command' => 'supervisorctl restart all',
            'type' => 'shell',
            'enabled' => false,
        ],
        'opcache_reset' => [
            'name' => 'Reset PHP OPcache',
            'command' => 'php -r "opcache_reset();"',
            'type' => 'shell',
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log File Viewer Configuration
    |--------------------------------------------------------------------------
    |
    | Define the log files available to view and parse.
    | Supports multiple types: Laravel, Nginx Access, Nginx Error, etc.
    |
    */
    'logs' => [
        'max_lines_per_page' => 100, // Safe streaming limit
        'files' => [
            'laravel' => [
                'name' => 'Laravel Application Log',
                'path' => storage_path('logs/laravel.log'),
                'type' => 'laravel',
            ],
            'nginx_access' => [
                'name' => 'Nginx Access Log',
                'path' => env('COREWATCH_NGINX_ACCESS_LOG', '/var/log/nginx/access.log'),
                'type' => 'nginx_access',
            ],
            'nginx_error' => [
                'name' => 'Nginx Error Log',
                'path' => env('COREWATCH_NGINX_ERROR_LOG', '/var/log/nginx/error.log'),
                'type' => 'nginx_error',
            ],
            'apache_access' => [
                'name' => 'Apache Access Log',
                'path' => env('COREWATCH_APACHE_ACCESS_LOG', '/var/log/apache2/access.log'),
                'type' => 'apache_access',
            ],
            'apache_error' => [
                'name' => 'Apache Error Log',
                'path' => env('COREWATCH_APACHE_ERROR_LOG', '/var/log/apache2/error.log'),
                'type' => 'apache_error',
            ],
        ],
    ],
];
