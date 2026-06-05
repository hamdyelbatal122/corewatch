# Production Deployment Checklist

## 1. Install & Configure

```bash
composer require hamzi/corewatch:^2.0
php artisan corewatch:install
```

## 2. Security (Required)

```php
// config/corewatch.php
'middleware' => ['web', 'auth'],
```

```php
// AppServiceProvider::boot()
config(['corewatch.gate' => fn ($request) => $request->user()?->isAdmin()]);
```

Disable dangerous shell commands unless explicitly needed:

```php
'redis_flush' => ['enabled' => false, ...],
'opcache_reset' => ['enabled' => false, ...],
```

## 3. Scheduler (Required for alerts & heartbeat)

```php
// routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('corewatch:heartbeat')->everyMinute();
Schedule::command('corewatch:check-health')->everyFiveMinutes();
```

Ensure server cron runs `* * * * * php artisan schedule:run`.

## 4. Alerting

```env
COREWATCH_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...
COREWATCH_CPU_THRESHOLD=85
COREWATCH_RAM_THRESHOLD=90
COREWATCH_DISK_THRESHOLD=90
```

## 5. Monitoring Integrations

| Endpoint | Use Case |
|----------|----------|
| `GET /corewatch/api/health` | Uptime Robot, Pingdom, K8s liveness |
| `GET /corewatch/api/metrics/prometheus` | Grafana, Prometheus scraper |

```env
COREWATCH_HEALTH_PUBLIC=false
COREWATCH_PROMETHEUS_PUBLIC=false
```

## 6. Performance

```env
COREWATCH_METRICS_CACHE_TTL=5
COREWATCH_RATE_LIMIT=60
```

## 7. Audit Trail

Service commands are logged to the default log channel with `[CoreWatch Audit]` prefix.
Configure a dedicated channel in `config/corewatch.php` → `audit_log.channel`.
