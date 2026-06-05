<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

interface SystemMetricsCollectorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function collect(): array;

    /**
     * @return array<string, mixed>
     */
    public function collectCpu(): array;

    /**
     * @return array<string, mixed>
     */
    public function collectRam(): array;

    /**
     * @return array<string, mixed>
     */
    public function collectDisk(): array;

    /**
     * @return array<string, string>
     */
    public function collectSystemInfo(): array;

    public function isShellDisabled(): bool;
}
