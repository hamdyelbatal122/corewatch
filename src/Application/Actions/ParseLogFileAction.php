<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\Actions;

use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;
use Hamzi\CoreWatch\Contracts\LogReaderInterface;

final class ParseLogFileAction
{
    public function __construct(
        private readonly LogReaderInterface $logReader,
    ) {}

    /**
     * @return array{logs: array<int, array<string, mixed>>, total_scanned: int, has_more: bool, error?: string}
     */
    public function execute(string $fileKey, int $page, LogFilterDto $filters): array
    {
        $filesConfig = config('corewatch.logs.files', []);

        if (! array_key_exists($fileKey, $filesConfig)) {
            return [
                'logs' => [],
                'total_scanned' => 0,
                'has_more' => false,
                'error' => 'Configured log file key not found.',
                'not_found' => true,
            ];
        }

        $logFile = $filesConfig[$fileKey];
        $limit = (int) config('corewatch.logs.max_lines_per_page', 100);

        $result = $this->logReader->read(
            $logFile['path'],
            $logFile['type'],
            $limit,
            $page,
            $filters
        );

        $result['file_name'] = $logFile['name'];
        $result['file_path'] = $logFile['path'];

        return $result;
    }
}
