<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Contracts;

use Hamzi\CoreWatch\Domain\ValueObjects\Alert;

interface AlertNotifierInterface
{
    /**
     * @param  array<string, Alert>  $alerts
     * @param  array<string, string>  $systemInfo
     */
    public function notify(array $alerts, array $systemInfo): bool;
}
