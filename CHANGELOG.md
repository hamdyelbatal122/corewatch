# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Clean Architecture layers: Contracts, Domain, Application, Infrastructure, Http
- Repository pattern for database stats, application health, and log file reading
- Application Actions: `GetServerMetricsAction`, `ParseLogFileAction`, `ExecuteServiceCommandAction`, `CheckHealthAndAlertAction`
- `EnsureCoreWatchAuthorized` middleware for centralized authorization
- Form Requests: `LogsRequest`, `ControlServiceRequest`
- `HealthThresholdEvaluator` domain service and `Alert` value object
- `CoreWatch` Facade and `CoreWatchManager` for programmatic developer access
- `GET /corewatch/api/health` endpoint for uptime monitors and K8s probes
- `ThresholdBreached` event for custom notification channel integration
- `docs/ARCHITECTURE.md`, `SECURITY.md`, and `pint.json`
- Unit tests for threshold evaluation and log parsing

### Changed
- Refactored monolithic `SystemMonitor` into focused collectors
- Refactored `LogParser` into `LogFileRepository`
- Extracted Slack/Telegram notifications into dedicated notifier classes
- `DashboardConfigDto` eliminates config duplication between Controller and Livewire

### Fixed
- Artisan queue restart command now uses `queue:restart` instead of `php artisan queue:restart`
- Authorization gate config documented correctly (callable via `config()` in AppServiceProvider)
- README badge links now point to the correct repository

## [1.0.0] - 2026-05-20

### Added
- Initial release: embedded DevOps dashboard for Laravel 11–13
- CPU, RAM, Disk, uptime, and process monitoring
- Memory-efficient backward-seeking log viewer
- Whitelisted service control panel
- Slack and Telegram alerting via `corewatch:check-health`
- Livewire component for Filament/Nova embedding
- Modular Blade partials architecture
