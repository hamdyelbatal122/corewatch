<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Application\DTOs;

final readonly class LogFilterDto
{
    public function __construct(
        public ?string $level = null,
        public ?string $ip = null,
        public ?int $status = null,
        public ?string $search = null,
        public ?string $dateStart = null,
        public ?string $dateEnd = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            level: $data['level'] ?? null,
            ip: $data['ip'] ?? null,
            status: isset($data['status']) ? (int) $data['status'] : null,
            search: $data['search'] ?? null,
            dateStart: $data['date_start'] ?? null,
            dateEnd: $data['date_end'] ?? null,
        );
    }
}
