<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Infrastructure\Notifications;

use Hamzi\CoreWatch\Domain\ValueObjects\Alert;

final class AlertDispatcher
{
    public function __construct(
        private readonly SlackNotifier $slack,
        private readonly TelegramNotifier $telegram,
    ) {}

    /**
     * @param  array<string, Alert>  $alerts
     * @param  array<string, string>  $systemInfo
     */
    public function dispatch(array $alerts, array $systemInfo): void
    {
        $channels = config('corewatch.notifications.channels', []);

        if (in_array('slack', $channels, true)) {
            $this->slack->notify($alerts, $systemInfo);
        }

        if (in_array('telegram', $channels, true)) {
            $this->telegram->notify($alerts, $systemInfo);
        }
    }
}
