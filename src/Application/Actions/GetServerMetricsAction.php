<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\Actions;

use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;
use Illuminate\Support\Facades\Cache;

final class GetServerMetricsAction
{
    private const CACHE_KEY = 'corewatch.metrics.snapshot';

    public function __construct(
        private readonly SystemMetricsCollectorInterface $collector,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $ttl = (int) config('corewatch.metrics_cache_ttl', 5);

        if ($ttl <= 0) {
            return $this->collector->collect();
        }

        return Cache::remember(self::CACHE_KEY, $ttl, fn () => $this->collector->collect());
    }
}
