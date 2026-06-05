# CoreWatch Architecture

CoreWatch is a Laravel package built on **Clean Architecture** principles. Each layer has a single responsibility and dependencies always point inward.

## Layer Diagram

```
┌─────────────────────────────────────────────────────────┐
│  Presentation (Http, Livewire, Console, Facade)         │
├─────────────────────────────────────────────────────────┤
│  Application (Actions, DTOs)                            │
├─────────────────────────────────────────────────────────┤
│  Domain (Value Objects, Enums, Domain Services)         │
├─────────────────────────────────────────────────────────┤
│  Infrastructure (Collectors, Repositories, Shell)      │
├─────────────────────────────────────────────────────────┤
│  Contracts (Interfaces — Dependency Inversion)          │
└─────────────────────────────────────────────────────────┘
```

## Data Flow: Metrics Request

```
Browser → DashboardController
       → GetServerMetricsAction
       → SystemMetricsCollectorInterface
       → [CpuMetricsCollector, RamMetricsCollector, ...]
       → /proc filesystem | ShellExecutor | Repositories
```

## Data Flow: Health Alert

```
Schedule → CheckHealthCommand
        → CheckHealthAndAlertAction
        → HealthThresholdEvaluator (Domain)
        → ThresholdBreached Event
        → AlertDispatcher → Slack / Telegram
```

## Key Design Decisions

| Decision | Rationale |
|----------|-----------|
| Collectors per metric | Single Responsibility; easy to test and extend |
| Repository for logs/DB | Isolate I/O from business logic |
| Actions as use cases | Thin controllers; reusable from Facade, CLI, jobs |
| Middleware for auth | DRY authorization across HTTP and Livewire |
| Events for alerts | Host apps can hook custom notification channels |

## Extending CoreWatch

### Add a custom metric collector

1. Create `Infrastructure/Collectors/MyCollector.php`
2. Register in `CoreWatchServiceProvider`
3. Add to `SystemMetricsCollector::collect()`

### Add a custom notification channel

Listen to `ThresholdBreached` in your app:

```php
Event::listen(ThresholdBreached::class, function (ThresholdBreached $event) {
    // Send to PagerDuty, Discord, etc.
});
```

### Programmatic access

```php
use Hamzi\CoreWatch\Facades\CoreWatch;

$metrics = CoreWatch::metrics();
$health  = CoreWatch::health(); // For uptime monitors
```
