<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Shell;

use Exception;
use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Illuminate\Support\Facades\Process;

final class LaravelShellExecutor implements ShellExecutorInterface
{
    public function run(string $command): array
    {
        if ($this->isDisabled()) {
            return ['success' => false, 'output' => 'Shell execution is disabled in php.ini'];
        }

        try {
            $processResult = Process::run($command);

            return [
                'success' => $processResult->successful(),
                'output' => $processResult->output().$processResult->errorOutput(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'output' => $e->getMessage(),
            ];
        }
    }

    public function isDisabled(): bool
    {
        $disabledFunctions = explode(',', ini_get('disable_functions'));
        $disabledFunctions = array_map('trim', $disabledFunctions);
        $disabledFunctions = array_map('strtolower', $disabledFunctions);

        return in_array('exec', $disabledFunctions, true)
            || in_array('shell_exec', $disabledFunctions, true)
            || in_array('system', $disabledFunctions, true)
            || in_array('proc_open', $disabledFunctions, true);
    }
}
