<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Jobs;

use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Hamzi\CoreWatch\Infrastructure\Notifications\SlackNotifier;
use Hamzi\CoreWatch\Infrastructure\Notifications\TelegramNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SendQueuedAlertJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30];

    /**
     * @param  array<string, Alert>  $alerts
     * @param  array<string, string>  $systemInfo
     * @param  array<int, string>  $channels
     */
    public function __construct(
        public readonly array $alerts,
        public readonly array $systemInfo,
        public readonly array $channels,
    ) {}

    public function handle(SlackNotifier $slack, TelegramNotifier $telegram): void
    {
        if (in_array('slack', $this->channels, true)) {
            $slack->notify($this->alerts, $this->systemInfo);
        }

        if (in_array('telegram', $this->channels, true)) {
            $telegram->notify($this->alerts, $this->systemInfo);
        }
    }
}
