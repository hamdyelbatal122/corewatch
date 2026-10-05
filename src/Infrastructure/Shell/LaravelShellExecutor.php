<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Shell;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Illuminate\Support\Facades\Process;
use Throwable;

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
        } catch (Throwable $e) {
            return [
                'success' => false,
                'output' => $e->getMessage(),
            ];
        }
    }

    public function isDisabled(): bool
    {
        $ini = ini_get('disable_functions');
        if (! is_string($ini) || trim($ini) === '') {
            return false;
        }

        $disabledFunctions = explode(',', strtolower($ini));
        $disabledFunctions = array_map('trim', $disabledFunctions);

        return in_array('exec', $disabledFunctions, true)
            || in_array('shell_exec', $disabledFunctions, true)
            || in_array('system', $disabledFunctions, true)
            || in_array('proc_open', $disabledFunctions, true);
    }
}
