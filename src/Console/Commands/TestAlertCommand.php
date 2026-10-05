<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Console\Commands;

use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;
use Hamzi\CoreWatch\Domain\Enums\AlertSeverity;
use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Hamzi\CoreWatch\Infrastructure\Notifications\AlertDispatcher;
use Illuminate\Console\Command;

final class TestAlertCommand extends Command
{
    protected $signature = 'corewatch:test-alert';

    protected $description = 'Dispatch a test notification to verify configured alert channels';

    public function handle(
        AlertDispatcher $dispatcher,
        SystemMetricsCollectorInterface $collector,
    ): int {
        $this->components->info('Dispatching CoreWatch test alert...');

        /** @var array<int, string> $channels */
        $channels = config('corewatch.notifications.channels', []);

        if (count($channels) === 0) {
            $this->components->warn('No notification channels are enabled in config/corewatch.php');

            return Command::FAILURE;
        }

        $testAlert = new Alert(
            key: 'test',
            name: 'CoreWatch Integration Test',
            current: '99.9%',
            threshold: '85.0%',
            severity: AlertSeverity::Warning,
            details: 'This is a verified test alert dispatched from artisan corewatch:test-alert.',
        );

        $systemInfo = $collector->collectSystemInfo();

        $dispatcher->dispatch(['test' => $testAlert], $systemInfo);

        $this->components->info('Test alert successfully dispatched to channels: '.implode(', ', $channels));

        return Command::SUCCESS;
    }
}
