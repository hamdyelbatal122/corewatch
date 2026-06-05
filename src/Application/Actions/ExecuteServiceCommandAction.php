<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\Actions;

use Exception;
use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

final class ExecuteServiceCommandAction
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array{success: bool, service?: string, output?: string, error?: string, not_found?: bool}
     */
    public function execute(string $serviceKey): array
    {
        $servicesConfig = config('corewatch.services', []);

        if (! array_key_exists($serviceKey, $servicesConfig)) {
            return [
                'success' => false,
                'error' => 'Unauthorized or unregistered command trigger.',
                'not_found' => true,
            ];
        }

        $service = $servicesConfig[$serviceKey];
        $cmdText = $service['command'];
        $cmdType = $service['type'];

        try {
            if ($cmdType === 'artisan') {
                $status = Artisan::call($cmdText);
                $output = Artisan::output();
                $success = ($status === 0);
            } else {
                if ($this->shell->isDisabled()) {
                    return [
                        'success' => false,
                        'error' => 'Shell execution is disabled in PHP configuration.',
                    ];
                }

                $processResult = Process::run($cmdText);
                $success = $processResult->successful();
                $output = $processResult->output() ?: $processResult->errorOutput();
            }

            return [
                'success' => $success,
                'service' => $service['name'],
                'output' => trim($output) ?: 'Command completed successfully (no output).',
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
