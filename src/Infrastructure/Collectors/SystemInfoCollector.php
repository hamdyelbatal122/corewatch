<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

final class SystemInfoCollector
{
    /**
     * @return array<string, string>
     */
    public function collect(): array
    {
        $os = PHP_OS;
        $kernel = php_uname('r');
        $hostname = php_uname('n');

        if (is_readable('/etc/os-release')) {
            $osInfo = file_get_contents('/etc/os-release');
            if ($osInfo !== false && preg_match('/PRETTY_NAME="([^"]+)"/', $osInfo, $matches)) {
                $os = $matches[1];
            }
        }

        return [
            'os' => $os,
            'kernel' => $kernel,
            'hostname' => $hostname,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Command Line Interface',
        ];
    }
}
