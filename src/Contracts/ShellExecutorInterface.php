<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

interface ShellExecutorInterface
{
    /**
     * @return array{success: bool, output: string}
     */
    public function run(string $command): array;

    public function isDisabled(): bool;
}
