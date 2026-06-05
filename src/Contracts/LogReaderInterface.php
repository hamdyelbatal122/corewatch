<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;

interface LogReaderInterface
{
    /**
     * @return array{logs: array<int, array<string, mixed>>, total_scanned: int, has_more: bool, error?: string}
     */
    public function read(string $path, string $type, int $limit, int $page, LogFilterDto $filters): array;
}
