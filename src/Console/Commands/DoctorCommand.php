<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Console\Commands;

use Hamzi\CoreWatch\Domain\Services\SystemDoctor;
use Illuminate\Console\Command;

final class DoctorCommand extends Command
{
    protected $signature = 'corewatch:doctor';

    protected $description = 'Inspect server telemetry prerequisites, security settings, and notifications';

    public function handle(SystemDoctor $doctor): int
    {
        $this->components->info('Running CoreWatch System Diagnostics...');
        $this->newLine();

        $checks = $doctor->runDiagnostics();

        $rows = [];
        $passed = 0;
        $warnings = 0;
        $failures = 0;

        foreach ($checks as $item) {
            $statusFormatted = match ($item['status']) {
                'PASS' => '<fg=green>PASS</>',
                'WARN' => '<fg=yellow>WARN</>',
                'FAIL' => '<fg=red>FAIL</>',
                default => $item['status'],
            };

            if ($item['status'] === 'PASS') {
                $passed++;
            } elseif ($item['status'] === 'WARN') {
                $warnings++;
            } else {
                $failures++;
            }

            $rows[] = [
                $item['category'],
                $item['check'],
                $statusFormatted,
                $item['detail'],
            ];
        }

        $this->table(
            ['Category', 'Check', 'Status', 'Diagnostic Detail'],
            $rows
        );

        $this->newLine();

        $summaryText = sprintf('Diagnostics completed: %d passed, %d warnings, %d failures.', $passed, $warnings, $failures);

        if ($failures > 0) {
            $this->components->error($summaryText);

            return Command::FAILURE;
        }

        if ($warnings > 0) {
            $this->components->warn($summaryText);

            return Command::SUCCESS;
        }

        $this->components->info($summaryText);

        return Command::SUCCESS;
    }
}
