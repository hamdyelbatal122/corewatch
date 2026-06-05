<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Domain\ValueObjects;

use Hamzi\CoreWatch\Domain\Enums\AlertSeverity;

final readonly class Alert
{
    public function __construct(
        public string $key,
        public string $name,
        public string $current,
        public string $threshold,
        public AlertSeverity $severity,
        public string $details,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'current' => $this->current,
            'threshold' => $this->threshold,
            'severity' => $this->severity->value,
            'details' => $this->details,
        ];
    }
}
