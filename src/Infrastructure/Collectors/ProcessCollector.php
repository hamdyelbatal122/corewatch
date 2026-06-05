<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;

final class ProcessCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<int, array<string, string>>
     */
    public function collect(): array
    {
        if ($this->shell->isDisabled()) {
            return [];
        }

        $result = $this->shell->run('ps -eo pcpu,pmem,pid,user,comm --sort=-pcpu | head -n 6');
        if (! $result['success']) {
            return [];
        }

        $lines = explode("\n", trim($result['output']));
        $processes = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if ($line === '') {
                continue;
            }
            $parts = preg_split('/\s+/', $line, 5);
            if (count($parts) >= 5) {
                $processes[] = [
                    'cpu' => $parts[0].'%',
                    'mem' => $parts[1].'%',
                    'pid' => $parts[2],
                    'user' => $parts[3],
                    'command' => $parts[4],
                ];
            }
        }

        return $processes;
    }
}
