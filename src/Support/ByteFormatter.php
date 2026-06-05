<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Support;

final class ByteFormatter
{
    public static function format(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2).' '.$units[$i];
    }

    public static function formatUptime(int $seconds): string
    {
        $days = (int) floor($seconds / 86400);
        $seconds %= 86400;
        $hours = (int) floor($seconds / 3600);
        $seconds %= 3600;
        $minutes = (int) floor($seconds / 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days}d";
        }
        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes}m";
        }

        return count($parts) > 0 ? implode(' ', $parts) : '0m';
    }
}
