<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

interface DatabaseStatsRepositoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function getStats(): array;
}
