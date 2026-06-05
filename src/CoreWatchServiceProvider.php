<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch;

use Hamzi\CoreWatch\Application\Actions\CheckHealthAndAlertAction;
use Hamzi\CoreWatch\Application\Actions\ExecuteServiceCommandAction;
use Hamzi\CoreWatch\Application\Actions\GetServerMetricsAction;
use Hamzi\CoreWatch\Application\Actions\ParseLogFileAction;
use Hamzi\CoreWatch\Console\Commands\CheckHealthCommand;
use Hamzi\CoreWatch\Console\Commands\HeartbeatCommand;
use Hamzi\CoreWatch\Console\Commands\InstallCommand;
use Hamzi\CoreWatch\Contracts\ApplicationHealthRepositoryInterface;
use Hamzi\CoreWatch\Contracts\DatabaseStatsRepositoryInterface;
use Hamzi\CoreWatch\Contracts\LogReaderInterface;
use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;
use Hamzi\CoreWatch\Domain\Services\HealthThresholdEvaluator;
use Hamzi\CoreWatch\Http\Controllers\DashboardController;
use Hamzi\CoreWatch\Http\Controllers\HealthController;
use Hamzi\CoreWatch\Http\Controllers\PrometheusController;
use Hamzi\CoreWatch\Http\Middleware\EnsureCoreWatchAuthorized;
use Hamzi\CoreWatch\Infrastructure\Audit\ServiceAuditLogger;
use Hamzi\CoreWatch\Infrastructure\Collectors\CpuMetricsCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\DiskMetricsCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\ProcessCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\RamMetricsCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\ScheduleHeartbeatCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\ServicesStatusCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\SslCertificateCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\SystemInfoCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\SystemMetricsCollector;
use Hamzi\CoreWatch\Infrastructure\Collectors\UptimeCollector;
use Hamzi\CoreWatch\Infrastructure\Exporters\PrometheusExporter;
use Hamzi\CoreWatch\Infrastructure\Notifications\AlertDispatcher;
use Hamzi\CoreWatch\Infrastructure\Notifications\SlackNotifier;
use Hamzi\CoreWatch\Infrastructure\Notifications\TelegramNotifier;
use Hamzi\CoreWatch\Infrastructure\Repositories\ApplicationHealthRepository;
use Hamzi\CoreWatch\Infrastructure\Repositories\DatabaseStatsRepository;
use Hamzi\CoreWatch\Infrastructure\Repositories\FailedJobsRepository;
use Hamzi\CoreWatch\Infrastructure\Repositories\LogFileRepository;
use Hamzi\CoreWatch\Infrastructure\Shell\LaravelShellExecutor;
use Hamzi\CoreWatch\Livewire\CoreWatchDashboard;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class CoreWatchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/corewatch.php',
            'corewatch'
        );

        $this->registerInfrastructure();
        $this->registerApplication();
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'corewatch');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'corewatch');
        Blade::directive('cw', function (string $expression): string {
            return "<?php echo \Hamzi\CoreWatch\Support\Translation::get({$expression}); ?>";
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/corewatch.php' => config_path('corewatch.php'),
            ], 'corewatch-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/corewatch'),
            ], 'corewatch-views');

            $this->publishes([
                __DIR__.'/../lang' => $this->app->langPath('vendor/corewatch'),
            ], 'corewatch-lang');

            $this->commands([
                InstallCommand::class,
                HeartbeatCommand::class,
                CheckHealthCommand::class,
            ]);
        }

        $this->configureRateLimiting();
        $this->registerMiddleware();
        $this->registerRoutes();

        $this->app->booted(function (): void {
            if (! class_exists(Livewire::class) || ! $this->app->bound('livewire.finder')) {
                return;
            }

            Livewire::component('corewatch-dashboard', CoreWatchDashboard::class);
        });
    }

    protected function registerInfrastructure(): void
    {
        $this->app->singleton(ShellExecutorInterface::class, LaravelShellExecutor::class);
        $this->app->singleton(DatabaseStatsRepositoryInterface::class, DatabaseStatsRepository::class);
        $this->app->singleton(ApplicationHealthRepositoryInterface::class, ApplicationHealthRepository::class);
        $this->app->singleton(LogReaderInterface::class, LogFileRepository::class);
        $this->app->singleton(FailedJobsRepository::class);
        $this->app->singleton(ServiceAuditLogger::class);
        $this->app->singleton(PrometheusExporter::class);

        $this->app->singleton(CpuMetricsCollector::class);
        $this->app->singleton(RamMetricsCollector::class);
        $this->app->singleton(DiskMetricsCollector::class);
        $this->app->singleton(UptimeCollector::class);
        $this->app->singleton(SystemInfoCollector::class);
        $this->app->singleton(ServicesStatusCollector::class);
        $this->app->singleton(ProcessCollector::class);
        $this->app->singleton(SslCertificateCollector::class);
        $this->app->singleton(ScheduleHeartbeatCollector::class);

        $this->app->singleton(SystemMetricsCollectorInterface::class, SystemMetricsCollector::class);

        $this->app->singleton(SlackNotifier::class);
        $this->app->singleton(TelegramNotifier::class);
        $this->app->singleton(AlertDispatcher::class);
    }

    protected function registerApplication(): void
    {
        $this->app->singleton(HealthThresholdEvaluator::class);
        $this->app->singleton(GetServerMetricsAction::class);
        $this->app->singleton(ParseLogFileAction::class);
        $this->app->singleton(ExecuteServiceCommandAction::class);
        $this->app->singleton(CheckHealthAndAlertAction::class);
        $this->app->singleton(CoreWatchManager::class);
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('corewatch', function (Request $request) {
            $limit = (int) config('corewatch.rate_limit', 60);

            return Limit::perMinute($limit)->by($request->user()?->id ?: $request->ip());
        });
    }

    protected function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('corewatch.auth', EnsureCoreWatchAuthorized::class);
    }

    protected function registerRoutes(): void
    {
        if (! config('corewatch.enabled', true)) {
            return;
        }

        $path = config('corewatch.path', 'corewatch');
        $middleware = array_merge(
            config('corewatch.middleware', ['web']),
            ['corewatch.auth', 'throttle:corewatch']
        );

        Route::prefix($path)
            ->middleware($middleware)
            ->as('corewatch.')
            ->group(function () {
                Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/api/metrics', [DashboardController::class, 'metrics'])->name('api.metrics');
                Route::get('/api/logs', [DashboardController::class, 'logs'])->name('api.logs');
                Route::post('/api/services/control', [DashboardController::class, 'controlService'])->name('api.services.control');
            });

        if (config('corewatch.health_endpoint.enabled', true)) {
            $healthMiddleware = config('corewatch.health_endpoint.public', false)
                ? array_merge(config('corewatch.middleware', ['web']), ['throttle:corewatch'])
                : $middleware;

            Route::prefix($path)
                ->middleware($healthMiddleware)
                ->as('corewatch.')
                ->group(function () {
                    Route::get('/api/health', HealthController::class)->name('api.health');
                });
        }

        if (config('corewatch.prometheus_endpoint.enabled', true)) {
            $prometheusMiddleware = config('corewatch.prometheus_endpoint.public', false)
                ? array_merge(config('corewatch.middleware', ['web']), ['throttle:corewatch'])
                : $middleware;

            Route::prefix($path)
                ->middleware($prometheusMiddleware)
                ->as('corewatch.')
                ->group(function () {
                    Route::get('/api/metrics/prometheus', PrometheusController::class)->name('api.metrics.prometheus');
                });
        }
    }
}
