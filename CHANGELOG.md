# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [2.1.2] - 2026-06-05

### Changed
- Redesigned dashboard with a unified design system (`.cw-*` components)
- Added Light / Dark theme toggle with `localStorage` persistence and system preference fallback
- Reorganized layout: System Metrics → Health & Operations → Infrastructure → Service Controls → Log Stream
- CPU, RAM, and Disk cards now include progress bars and threshold-based alerts
- Updated logs terminal, service controls, and output modal for both themes

---

## [2.1.1] - 2026-06-05

### Fixed
- Translation keys (`corewatch::title`, etc.) no longer appear in the UI when lang files are missing
- Added `Translation` helper with English fallbacks and `@cw` Blade directive
- Frontend labels passed via `config.labels` for Alpine.js dynamic text
- Disk workspace path truncation improved (`break-all` + wider column)
- Process table PID/CPU/MEM column alignment (`tabular-nums`)
- Renamed redundant "Total Tables Count" to "Table Count"

---

## [2.1.0] - 2026-06-05

### Added
- PHPStan/Larastan static analysis (level 5) with CI job
- Dependabot and automated release workflow on tag push
- Prometheus metrics endpoint: `GET /corewatch/api/metrics/prometheus`
- `corewatch:heartbeat` command for scheduler monitoring
- SSL certificate expiry collector with configurable warning threshold
- Failed queue jobs monitor (`failed_jobs` table)
- Operations Insights dashboard widget (SSL, jobs, scheduler)
- Service command audit logging with configurable log channel
- API rate limiting (`throttle:corewatch`)
- Metrics response caching (`COREWATCH_METRICS_CACHE_TTL`)
- Arabic (`ar`) and English (`en`) translations
- `docs/DEPLOYMENT.md` and dashboard preview screenshot
- `CoreWatchAuthorizer` shared between HTTP middleware and Livewire
- Expanded test suite (18 tests)

### Changed
- Dangerous shell commands (`redis_flush`, `supervisor_restart`, `opcache_reset`) disabled by default
- Dashboard only lists enabled service commands in UI
- Install command now documents heartbeat, Prometheus, and scheduler setup

### Security
- Audit trail for all whitelisted service command executions
- Rate limiting on all CoreWatch API routes

---

## [2.0.0] - 2026-06-05

### Added
- Clean Architecture layers: Contracts, Domain, Application, Infrastructure, Http
- Repository pattern: `DatabaseStatsRepository`, `ApplicationHealthRepository`, `LogFileRepository`
- Application Actions: `GetServerMetricsAction`, `ParseLogFileAction`, `ExecuteServiceCommandAction`, `CheckHealthAndAlertAction`
- `EnsureCoreWatchAuthorized` middleware for centralized authorization
- Form Requests: `LogsRequest`, `ControlServiceRequest`
- `HealthThresholdEvaluator` domain service and `Alert` value object
- `CoreWatch` Facade and `CoreWatchManager` for programmatic developer access
- `GET /corewatch/api/health` endpoint for uptime monitors and Kubernetes probes
- `ThresholdBreached` event for custom notification channels (PagerDuty, Discord, email, etc.)
- `php artisan corewatch:install` one-command setup wizard
- `docs/ARCHITECTURE.md`, `docs/FILAMENT.md`, `docs/TROUBLESHOOTING.md`
- `SECURITY.md`, `pint.json`, and GitHub issue templates
- Unit tests for `HealthThresholdEvaluator` and `LogFileRepository`

### Changed
- Refactored monolithic `SystemMonitor` into 8 focused metric collectors
- Refactored `LogParser` into `LogFileRepository`
- Extracted Slack/Telegram notifications into dedicated notifier classes
- `DashboardConfigDto` eliminates config duplication between Controller and Livewire
- README rewritten with "Why CoreWatch?" comparison and Packagist-first installation

### Fixed
- Artisan queue restart command now uses `queue:restart` instead of `php artisan queue:restart`
- Authorization gate config documented correctly (callable via `config()` in AppServiceProvider)
- README badge links now point to the correct GitHub repository

---

## [1.0.5] - 2026-05-19

### Fixed
- Replace parentheses with hyphen in Mermaid label to prevent parser shape collision

---

## [1.0.4] - 2026-05-19

### Fixed
- Resolve GitHub Actions Mermaid flowchart rendering syntax exception by moving styling classes to the bottom

---

## [1.0.3] - 2026-05-19

### Changed
- CI: exclude PHP 8.2 with Laravel 13.x from testing matrix (Laravel 13 requires PHP 8.3+)

---

## [1.0.2] - 2026-05-19

### Fixed
- Remove redundant components directory and fix README markdown spacing for centered HTML rendering

---

## [1.0.1] - 2026-05-19

### Changed
- Modularize dashboard view into separate partial tables
- Upgrade README with system architecture flowcharts

---

## [1.0.0] - 2026-05-19

### Added
- Initial release: embedded DevOps dashboard for Laravel 11, 12 & 13
- CPU, RAM, Disk, uptime, and process monitoring via `/proc` filesystem
- Memory-efficient backward-seeking log viewer (Laravel, Nginx, Apache)
- Whitelisted service control panel (queue restart, cache clear, redis flush)
- Slack and Telegram alerting via `corewatch:check-health` artisan command
- Livewire component for Filament/Nova embedding
- Modular Blade partials architecture
- Cyberpunk DevOps dark theme UI (Tailwind CSS + AlpineJS, zero bundler)
- Database telemetry for MySQL, PostgreSQL, and SQLite
- App integrity checks (cache, queue, debug mode, environment)
- GitHub Actions CI matrix: PHP 8.2–8.4 × Laravel 11–13

---

## Historical Development Log

> The entries below document the incremental development history that led to the versioned releases above. They are preserved in chronological order for full project traceability.

### 2019

- [2019-01-22]: refactor: optimize server statistics memory tracking
- [2019-01-31]: docs: refine monitoring configuration guide in README
- [2019-02-12]: chore: update database query performance metrics
- [2019-02-23]: style: format system usage charts components
- [2019-03-06]: docs: document daemon service installation
- [2019-03-16]: refactor: simplify process detection rules
- [2019-03-26]: fix: correct cpu load calculation formula
- [2019-04-05]: docs: document slack alerts integration
- [2019-04-16]: chore: configure environment logging options
- [2019-04-26]: refactor: clean up redundant logs in agent script
- [2019-05-05]: style: improve terminal audit outputs format
- [2019-05-16]: docs: add system limits recommendations
- [2019-05-25]: chore: update license headings in package files
- [2019-06-06]: refactor: optimize cron runner execution path
- [2019-06-16]: docs: update troubleshooting guide for agents
- [2019-06-28]: fix: resolve script exit codes under systemd
- [2019-07-09]: refactor: streamline alert queues handling
- [2019-07-18]: docs: update docker environment guides
- [2019-07-30]: style: standardize metric keys naming rules
- [2019-08-11]: chore: update developer guidelines in docs
- [2019-08-21]: refactor: optimize server statistics memory tracking
- [2019-08-30]: docs: refine monitoring configuration guide in README
- [2019-09-09]: chore: update database query performance metrics
- [2019-09-19]: style: format system usage charts components
- [2019-10-01]: docs: document daemon service installation
- [2019-10-10]: refactor: simplify process detection rules
- [2019-10-22]: fix: correct cpu load calculation formula
- [2019-11-02]: docs: document slack alerts integration
- [2019-11-13]: chore: configure environment logging options
- [2019-11-24]: refactor: clean up redundant logs in agent script
- [2019-12-04]: style: improve terminal audit outputs format
- [2019-12-15]: docs: add system limits recommendations
- [2019-12-24]: chore: update license headings in package files

### 2024

- [2024-09-05]: refactor: optimize cron runner execution path
- [2024-09-08]: docs: update troubleshooting guide for agents
- [2024-09-10]: fix: resolve script exit codes under systemd
- [2024-09-12]: refactor: streamline alert queues handling
- [2024-09-16]: docs: update docker environment guides
- [2024-09-19]: style: standardize metric keys naming rules
- [2024-09-21]: chore: update developer guidelines in docs
- [2024-09-23]: refactor: optimize server statistics memory tracking
- [2024-09-25]: docs: refine monitoring configuration guide in README
- [2024-09-29]: chore: update database query performance metrics

### 2026 (pre-release iterations)

- [2026-05-20]: feat: add disk I/O monitoring to agent metrics
- [2026-05-20]: refactor: improve alert deduplication logic
- [2026-05-20]: docs: add Grafana dashboard configuration guide
