<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Collectors;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;

final class ServicesStatusCollector
{
    public function __construct(
        private readonly ShellExecutorInterface $shell,
    ) {}

    /**
     * @return array<string, array<string, mixed>>
     */
    public function collect(): array
    {
        $services = [
            'nginx' => ['name' => 'Nginx', 'port' => 80, 'process' => 'nginx'],
            'apache' => ['name' => 'Apache', 'port' => 8080, 'process' => 'apache2'],
            'mysql' => ['name' => 'MySQL', 'port' => 3306, 'process' => 'mysqld'],
            'redis' => ['name' => 'Redis', 'port' => 6379, 'process' => 'redis-server'],
            'supervisor' => ['name' => 'Supervisor', 'port' => null, 'process' => 'supervisord'],
            'memcached' => ['name' => 'Memcached', 'port' => 11211, 'process' => 'memcached'],
        ];

        $statusList = [];

        foreach ($services as $key => $meta) {
            $isActive = false;

            $check = $this->shell->run('pgrep -x '.escapeshellarg($meta['process']));
            if ($check['success'] && ! empty(trim($check['output']))) {
                $isActive = true;
            }

            if (! $isActive && $meta['port'] !== null) {
                $connection = @fsockopen('127.0.0.1', $meta['port'], $errno, $errstr, 0.2);
                if (is_resource($connection)) {
                    $isActive = true;
                    fclose($connection);
                }
            }

            if ($key === 'supervisor' && ! $isActive) {
                $checkSup = $this->shell->run('supervisorctl status');
                if ($checkSup['success'] && ! str_contains(strtolower($checkSup['output']), 'error')) {
                    $isActive = true;
                }
            }

            $statusList[$key] = [
                'name' => $meta['name'],
                'active' => $isActive,
                'port' => $meta['port'],
            ];
        }

        return $statusList;
    }
}
