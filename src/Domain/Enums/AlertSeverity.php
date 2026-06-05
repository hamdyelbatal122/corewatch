<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Domain\Enums;

enum AlertSeverity: string
{
    case Critical = 'critical';
    case Warning = 'warning';
    case Info = 'info';
}
