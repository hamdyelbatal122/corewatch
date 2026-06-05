<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\Actions;

use Hamzi\CoreWatch\Contracts\SystemMetricsCollectorInterface;

final class GetServerMetricsAction
{
    public function __construct(
        private readonly SystemMetricsCollectorInterface $collector,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        return $this->collector->collect();
    }
}
