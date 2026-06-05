<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Repositories;

use DateTime;
use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;
use Hamzi\CoreWatch\Contracts\LogReaderInterface;

final class LogFileRepository implements LogReaderInterface
{
    public function read(string $path, string $type, int $limit, int $page, LogFilterDto $filters): array
    {
        if (! is_file($path) || ! is_readable($path)) {
            return [
                'logs' => [],
                'total_scanned' => 0,
                'has_more' => false,
                'error' => "Log file does not exist or is not readable: {$path}",
            ];
        }

        $logs = [];
        $chunkSize = 1024 * 64;
        $handle = fopen($path, 'r');
        if (! $handle) {
            return ['logs' => [], 'total_scanned' => 0, 'has_more' => false, 'error' => 'Unable to open file'];
        }

        fseek($handle, 0, SEEK_END);
        $fileSize = ftell($handle);
        $position = $fileSize;

        $leftover = '';
        $matchedCount = 0;
        $skipCount = ($page - 1) * $limit;
        $totalScanned = 0;
        $hasMore = false;
        $currentStack = [];

        while ($position > 0 && count($logs) < $limit) {
            $readSize = min($position, $chunkSize);
            $position -= $readSize;

            fseek($handle, $position, SEEK_SET);
            $buffer = fread($handle, $readSize);
            if ($buffer === false) {
                break;
            }

            $buffer .= $leftover;
            $lines = explode("\n", $buffer);

            if ($position > 0) {
                $leftover = array_shift($lines);
            } else {
                $leftover = '';
            }

            for ($i = count($lines) - 1; $i >= 0; $i--) {
                $line = trim($lines[$i]);
                if ($line === '') {
                    continue;
                }

                $totalScanned++;

                $parsed = $this->parseLine($line, $type);

                if ($parsed !== null) {
                    if ($type === 'laravel' && ! empty($currentStack)) {
                        $stackMsg = implode("\n", array_reverse($currentStack));
                        $parsed['message'] .= "\n".$stackMsg;
                        $currentStack = [];
                    }

                    if ($this->matchesFilters($parsed, $filters)) {
                        if ($skipCount > 0) {
                            $skipCount--;
                        } else {
                            $logs[] = $parsed;
                            $matchedCount++;
                            if ($matchedCount >= $limit) {
                                $hasMore = ($position > 0 || $i > 0);
                                break 2;
                            }
                        }
                    }
                } elseif ($type === 'laravel') {
                    $currentStack[] = $line;
                }
            }
        }

        if ($type === 'laravel' && ! empty($currentStack) && count($logs) > 0) {
            $lastIndex = count($logs) - 1;
            $stackMsg = implode("\n", array_reverse($currentStack));
            $logs[$lastIndex]['message'] .= "\n".$stackMsg;
        }

        fclose($handle);

        return [
            'logs' => $logs,
            'total_scanned' => $totalScanned,
            'has_more' => $hasMore,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseLine(string $line, string $type): ?array
    {
        return match ($type) {
            'laravel' => $this->parseLaravelLine($line),
            'nginx_access', 'apache_access' => $this->parseAccessLine($line),
            'nginx_error', 'apache_error' => $this->parseErrorLine($line, $type),
            default => [
                'date' => null,
                'level' => 'info',
                'message' => $line,
                'raw' => $line,
            ],
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseLaravelLine(string $line): ?array
    {
        $pattern = '/^\[(?<date>\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (?<env>\w+)\.(?<level>\w+): (?<message>.*)/s';
        if (preg_match($pattern, $line, $matches)) {
            return [
                'date' => $matches['date'],
                'env' => $matches['env'],
                'level' => strtoupper($matches['level']),
                'message' => trim($matches['message']),
                'raw' => $line,
            ];
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseAccessLine(string $line): ?array
    {
        $pattern = '/^(?<ip>\S+) \S+ \S+ \[(?<date>[^\]]+)\] "(?<method>\S+)\s+(?<url>\S+)\s+(?<protocol>[^"]+)" (?<status>\d{3}) (?<bytes>\S+)( "(?<referrer>[^"]*)")?( "(?<user_agent>[^"]*)")?/';

        if (preg_match($pattern, $line, $matches)) {
            $formattedDate = $this->parseCommonLogFormatDate($matches['date']);
            $status = (int) $matches['status'];

            $level = 'INFO';
            if ($status >= 500) {
                $level = 'ERROR';
            } elseif ($status >= 400) {
                $level = 'WARNING';
            }

            return [
                'date' => $formattedDate,
                'ip' => $matches['ip'],
                'method' => $matches['method'],
                'url' => $matches['url'],
                'status' => $status,
                'bytes' => $matches['bytes'],
                'level' => $level,
                'message' => sprintf('%s %s - HTTP %d (%s)', $matches['method'], $matches['url'], $status, $matches['bytes']),
                'raw' => $line,
            ];
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function parseErrorLine(string $line, string $type): ?array
    {
        if (str_contains($type, 'nginx')) {
            $pattern = '/^(?<date>\d{4}\/\d{2}\/\d{2} \d{2}:\d{2}:\d{2}) \[(?<level>[^\]]+)\] (?<message>.*)/';
            if (preg_match($pattern, $line, $matches)) {
                return [
                    'date' => str_replace('/', '-', $matches['date']),
                    'level' => strtoupper($matches['level']),
                    'message' => trim($matches['message']),
                    'raw' => $line,
                ];
            }
        } else {
            $pattern = '/^\[[^\]]+ (?<date>\w{3} \d{2} \d{2}:\d{2}:\d{2}\.\d+ \d{4})\] \[[^:]+:(?<level>\w+)\] (\[pid \d+\] )?(\[client (?<ip>[^\]]+)\] )?(?<message>.*)/';
            if (preg_match($pattern, $line, $matches)) {
                $dateTime = DateTime::createFromFormat('M d H:i:s.u Y', $matches['date']);
                $dateStr = $dateTime ? $dateTime->format('Y-m-d H:i:s') : $matches['date'];

                return [
                    'date' => $dateStr,
                    'level' => strtoupper($matches['level']),
                    'ip' => $matches['ip'] ?? null,
                    'message' => trim($matches['message']),
                    'raw' => $line,
                ];
            }
        }

        return null;
    }

    private function matchesFilters(array $entry, LogFilterDto $filters): bool
    {
        if ($filters->level !== null) {
            if (strtoupper($entry['level'] ?? '') !== strtoupper($filters->level)) {
                return false;
            }
        }

        if ($filters->ip !== null) {
            if (empty($entry['ip']) || ! str_contains($entry['ip'], $filters->ip)) {
                return false;
            }
        }

        if ($filters->status !== null) {
            if (empty($entry['status']) || (int) $entry['status'] !== $filters->status) {
                return false;
            }
        }

        if ($filters->search !== null) {
            $query = strtolower($filters->search);
            $msg = strtolower($entry['message'] ?? '');
            $raw = strtolower($entry['raw'] ?? '');
            if (! str_contains($msg, $query) && ! str_contains($raw, $query)) {
                return false;
            }
        }

        if (! empty($entry['date'])) {
            $entryTime = strtotime($entry['date']);
            if ($entryTime !== false) {
                if ($filters->dateStart !== null) {
                    $startTime = strtotime($filters->dateStart);
                    if ($startTime !== false && $entryTime < $startTime) {
                        return false;
                    }
                }
                if ($filters->dateEnd !== null) {
                    $endTime = strtotime($filters->dateEnd);
                    if ($endTime !== false && $entryTime > $endTime) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function parseCommonLogFormatDate(string $clfDate): string
    {
        $parts = explode(' ', $clfDate);
        $dateTimeStr = $parts[0] ?? '';
        $dateTime = DateTime::createFromFormat('d/M/Y:H:i:s', $dateTimeStr);
        if ($dateTime) {
            return $dateTime->format('Y-m-d H:i:s');
        }

        return $clfDate;
    }
}
