<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Console\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature = 'corewatch:install
                            {--views : Also publish Blade views for customization}
                            {--force : Overwrite existing published files}';

    protected $description = 'Install CoreWatch: publish config, verify environment, and show next steps';

    public function handle(): int
    {
        $this->components->info('Installing CoreWatch DevOps Sentinel...');
        $this->newLine();

        $configFlag = $this->option('force') ? '--force' : '';
        $this->call('vendor:publish', [
            '--tag' => 'corewatch-config',
            '--force' => $this->option('force'),
        ]);

        if ($this->option('views')) {
            $this->call('vendor:publish', [
                '--tag' => 'corewatch-views',
                '--force' => $this->option('force'),
            ]);
        }

        $this->newLine();
        $this->components->info('CoreWatch installed successfully!');
        $this->newLine();

        $path = config('corewatch.path', 'corewatch');
        $appUrl = config('app.url', 'http://localhost');

        $this->components->twoColumnDetail('Dashboard URL', "{$appUrl}/{$path}");
        $this->components->twoColumnDetail('Health Endpoint', "{$appUrl}/{$path}/api/health");
        $this->components->twoColumnDetail('Metrics API', "{$appUrl}/{$path}/api/metrics");

        $this->newLine();
        $this->components->warn('Production checklist:');
        $this->line('  1. Add <fg=cyan>auth</> middleware in <fg=cyan>config/corewatch.php</>');
        $this->line('  2. Set a gate callback in AppServiceProvider for fine-grained access');
        $this->line('  3. Configure Slack/Telegram alerts in <fg=cyan>.env</>');
        $this->line('  4. Schedule <fg=cyan>corewatch:check-health</> in routes/console.php');
        $this->newLine();
        $this->line('  <fg=gray>Schedule::command(\'corewatch:check-health\')->everyFiveMinutes();</>');
        $this->newLine();

        $this->components->info('Documentation: https://github.com/hamdyelbatal122/CoreWatch');

        return Command::SUCCESS;
    }
}
