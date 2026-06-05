<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Console\Commands;

use Hamzi\CoreWatch\Application\Actions\CheckHealthAndAlertAction;
use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Illuminate\Console\Command;

final class CheckHealthCommand extends Command
{
    protected $signature = 'corewatch:check-health';

    protected $description = 'Inspect server resource usage thresholds and trigger Slack/Telegram alerts';

    public function __construct(
        private readonly CheckHealthAndAlertAction $checkHealth,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking server resources...');

        $result = $this->checkHealth->execute();
        $alerts = $result['alerts'];
        $thresholds = $result['thresholds'];

        if (count($alerts) > 0) {
            $this->error(sprintf('Threshold breached! %d resource alerts found.', count($alerts)));
            $this->displayAlertsTable($alerts);
        } else {
            $this->info('Server health check completed successfully. All metrics are within safe boundaries.');
            $this->table(
                ['Resource', 'Current Usage', 'Alert Threshold', 'Status'],
                [
                    ['CPU', $result['cpu']['usage_percentage'].'%', $thresholds['cpu'].'%', 'OK ✅'],
                    ['RAM', $result['ram']['usage_percentage'].'%', $thresholds['ram'].'%', 'OK ✅'],
                    ['Disk', $result['disk']['usage_percentage'].'%', $thresholds['disk'].'%', 'OK ✅'],
                ]
            );
        }

        return Command::SUCCESS;
    }

    /**
     * @param  array<string, Alert>  $alerts
     */
    protected function displayAlertsTable(array $alerts): void
    {
        $rows = [];
        foreach ($alerts as $alert) {
            $rows[] = [
                $alert->name,
                $alert->current,
                $alert->threshold,
                strtoupper($alert->severity->value).' ⚠️',
                $alert->details,
            ];
        }

        $this->table(
            ['Metric Alert', 'Current', 'Limit', 'Severity', 'Context Details'],
            $rows
        );
    }
}
