<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Support\ByteFormatter;

final class UptimeCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    public function collect(): string
    {
        if (is_readable('/proc/uptime')) {
            $uptimeStr = file_get_contents('/proc/uptime');
            if ($uptimeStr !== false) {
                $seconds = (int) explode(' ', trim($uptimeStr))[0];

                return ByteFormatter::formatUptime($seconds);
            }
        }

        $result = $this->shell->run('uptime -p');
        if ($result['success']) {
            return trim(str_replace('up ', '', $result['output']));
        }

        return 'Unknown';
    }
}
