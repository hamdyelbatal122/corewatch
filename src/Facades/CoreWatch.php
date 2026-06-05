<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Facades;

use Hamzi\CoreWatch\CoreWatchManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> metrics()
 * @method static array<string, mixed> cpu()
 * @method static array<string, mixed> ram()
 * @method static array<string, mixed> disk()
 * @method static array<string, mixed> health()
 * @method static array<string, mixed> readLogs(string $fileKey, int $page = 1, ?\Hamzi\CoreWatch\Application\DTOs\LogFilterDto $filters = null)
 * @method static array<string, mixed> runService(string $serviceKey)
 * @method static array<string, mixed> checkHealth()
 *
 * @see CoreWatchManager
 */
final class CoreWatch extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CoreWatchManager::class;
    }
}
