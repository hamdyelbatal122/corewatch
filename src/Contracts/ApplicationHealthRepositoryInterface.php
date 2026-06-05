<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

interface ApplicationHealthRepositoryInterface
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function getChecks(): array;
}
