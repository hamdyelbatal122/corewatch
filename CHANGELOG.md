# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

---

## [2.2.0] - 2026-10-05

## Summary
Release 2.2.0 introduces system diagnostic tooling, asynchronous background alert queuing, static analysis upgrades to PHPStan Level 8, and enhanced system resilience with strict Throwable exception handling across all collectors and repositories.

## Changes
- Added `SystemDoctor` service and `php artisan corewatch:doctor` command for comprehensive server diagnostics across System, Database, Cache, Queue, Scheduler, Alerts, and Security.
- Added `php artisan corewatch:test-alert` command with interactive diagnostic feedback to verify Slack and Telegram channel delivery.
- Added asynchronous alert dispatching via `SendQueuedAlertJob` implementing `ShouldQueue` with backoff retries, configurable via `COREWATCH_NOTIFICATIONS_QUEUE`.
- Added `doctor()` method to `CoreWatchManager` and `CoreWatch` Facade for programmatic diagnostic checks.
- Hardened exception handling across collectors, controllers, repositories, and actions from `\Exception` to `\Throwable` to prevent unhandled fatal application crashes.
- Upgraded PHPStan static analysis configuration to Level 8 with zero errors.
- Resolved Composer dependency security advisories and expanded test suite to 32 tests with 211 assertions.
- Enforced clean code standards and removed emoji artifacts across all code, commands, and repository documentation.

---

## [2.1.7] - 2026-06-06

## Summary
Patch release restoring repository assets and visual documentation for package consumers.

## Changes
- Restored dashboard preview screenshot in visual documentation (`docs/images/dashboard-preview.png`).

---

## [2.1.6] - 2026-06-05

## Summary
Documentation asset refresh updating the visual overview of the operational interface.

## Changes
- Updated README dashboard preview screenshot (`docs/images/dashboard-preview.png`) to reflect current user interface styling.

---

## [2.1.5] - 2026-06-05

## Summary
Display fix correcting version string formatting within the dashboard header.

## Changes
- Fixed duplicate version prefix rendering (`vv2.1.4`) by displaying Composer version string directly without redundant prefixing.

---

## [2.1.4] - 2026-06-05

## Summary
Runtime compatibility hotfix ensuring uninterrupted support across PHP 8.2 environments.

## Changes
- Removed typed class constant from `PackageVersion` helper to maintain compatibility across supported PHP 8.2 runtimes.

---

## [2.1.3] - 2026-06-05

## Summary
Compatibility release adding Livewire 4 support and streamlining header interface elements.

## Changes
- Added `PackageVersion` helper to resolve active installed package version directly from Composer metadata.
- Added compatibility support for `livewire/livewire` ^3.4 and ^4.0 in dev dependencies.
- Deferred Livewire component registration until container resolves `livewire.finder` to prevent boot errors in testing environments.
- Cleaned header interface by removing redundant visual boxes and unrendered icons.

---

## [2.1.2] - 2026-06-05

## Summary
Visual and thematic overhaul introducing unified component design system and full light/dark theme support.

## Changes
- Redesigned interface using a structured component system (`.cw-*` class naming).
- Added persistent Light and Dark theme toggle with system preference fallback and localStorage storage.
- Restructured dashboard sections: System Metrics, Health and Operations, Infrastructure, Service Controls, and Log Stream.
- Added visual progress indicators and threshold alert states to CPU, RAM, and Disk metric cards.
- Updated styling across log terminal, service command controls, and output modals for both color themes.

---

## [2.1.1] - 2026-06-05

## Summary
Hotfix addressing localization resolution and visual column alignment across monitoring panels.

## Changes
- Prevented raw translation keys from rendering in dashboard views when language files are not published.
- Added `Translation` helper with built-in English fallbacks and `@cw` Blade directive.
- Bound frontend labels to Alpine.js configuration object for reactive localized text rendering.
- Improved workspace path display on disk storage cards to avoid aggressive truncation.
- Standardized tabular numeral alignment for process table metrics (PID, CPU, and Memory).
- Renamed redundant database metric label from "Total Tables Count" to "Table Count".

---

## [2.1.0] - 2026-06-05

## Summary
Major feature release adding Prometheus metric export, SSL certificate monitoring, failed queue jobs tracking, scheduler heartbeat detection, and Arabic localization.

## Changes
- Added Prometheus metrics export endpoint at `/corewatch/api/metrics/prometheus`.
- Added SSL certificate expiration collector with configurable warning threshold.
- Added failed queue jobs monitor probing the `failed_jobs` table.
- Added `corewatch:heartbeat` command and scheduler heartbeat tracking widget.
- Added service command execution audit logging with configurable log channel.
- Added API rate limiting (`throttle:corewatch`) on all package routes.
- Added response caching for system metrics via `COREWATCH_METRICS_CACHE_TTL`.
- Added full Arabic (`ar`) and English (`en`) localization with publishing support.
- Added shared `CoreWatchAuthorizer` authorization contract for HTTP and Livewire contexts.
- Enforced PHPStan Level 5 static analysis in CI and added Dependabot configuration.
- Set dangerous service commands (`redis_flush`, `supervisor_restart`, `opcache_reset`) to disabled by default.

---

## [2.0.0] - 2026-06-05

## Summary
Architecture redesign transitioning CoreWatch into a modular Clean Architecture package with developer facade, health probe endpoint, and extensible domain events.

## Changes
- Implemented Clean Architecture layers: Contracts, Domain, Application, Infrastructure, and Http presentation.
- Introduced repository abstraction pattern for database stats, health checks, and log streaming.
- Added application actions: `GetServerMetricsAction`, `ParseLogFileAction`, `ExecuteServiceCommandAction`, and `CheckHealthAndAlertAction`.
- Added `CoreWatch` Facade and `CoreWatchManager` for programmatic developer and monitoring access.
- Added `GET /corewatch/api/health` endpoint returning RFC-compliant status for load balancers and Kubernetes probes.
- Added `ThresholdBreached` event enabling integration with third-party incident management systems.
- Added `php artisan corewatch:install` automated setup wizard with production deployment checks.
- Refactored monolithic system monitor into dedicated single-responsibility metric collectors.
- Fixed queue restart command mapping to execute `queue:restart` without redundant binary prefixes.
- Published comprehensive documentation guides for Architecture, Filament integration, and Security policy.

---

## [1.0.5] - 2026-05-19

## Summary
Syntax fix resolving Mermaid architecture diagram rendering collisions in markdown parsers.

## Changes
- Replaced parentheses in Mermaid flowchart labels with hyphens to prevent shape delimiter collisions.

---

## [1.0.4] - 2026-05-19

## Summary
CI pipeline fix resolving Mermaid diagram parsing exceptions in GitHub rendering.

## Changes
- Relocated flowchart styling class definitions to document footer to resolve parser syntax exceptions.

---

## [1.0.3] - 2026-05-19

## Summary
Matrix configuration update for GitHub Actions continuous integration.

## Changes
- Excluded incompatible PHP 8.2 and Laravel 13.x combinations from the automated test matrix.

---

## [1.0.2] - 2026-05-19

## Summary
Package structure cleanup and markdown layout corrections.

## Changes
- Removed obsolete component directory artifacts.
- Corrected spacing syntax in documentation files for consistent markdown and HTML rendering.

---

## [1.0.1] - 2026-05-19

## Summary
Modularization of dashboard Blade views and documentation expansion.

## Changes
- Separated monolithic dashboard Blade view into individual, reusable component partials.
- Added architecture flowcharts to repository documentation.

---

## [1.0.0] - 2026-05-19

## Summary
Initial release of CoreWatch embedded DevOps monitoring dashboard for Laravel applications.

## Changes
- Added real-time CPU, RAM, Disk, Uptime, and Process monitoring via `/proc` filesystem and shell fallbacks.
- Added memory-efficient backward-seeking log parser supporting Laravel, Nginx, and Apache logs.
- Added whitelisted service command control panel for safe queue and cache maintenance.
- Added Slack and Telegram threshold alert delivery via `corewatch:check-health` Artisan command.
- Added standalone dashboard route, modular Blade partials, and Livewire component for admin panel integration.
- Added database telemetry support for MySQL, PostgreSQL, and SQLite.
- Added multi-environment GitHub Actions test matrix across PHP 8.2 through 8.4 and Laravel 11 through 13.

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
