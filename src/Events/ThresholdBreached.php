<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Events;

use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ThresholdBreached
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, Alert>  $alerts
     * @param  array<string, string>  $systemInfo
     */
    public function __construct(
        public readonly array $alerts,
        public readonly array $systemInfo,
    ) {}
}
