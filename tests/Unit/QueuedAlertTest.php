<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Domain\Enums\AlertSeverity;
use Hamzi\CoreWatch\Domain\ValueObjects\Alert;
use Hamzi\CoreWatch\Infrastructure\Notifications\AlertDispatcher;
use Hamzi\CoreWatch\Infrastructure\Notifications\SlackNotifier;
use Hamzi\CoreWatch\Infrastructure\Notifications\TelegramNotifier;
use Hamzi\CoreWatch\Jobs\SendQueuedAlertJob;
use Hamzi\CoreWatch\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;

final class QueuedAlertTest extends TestCase
{
    #[Test]
    public function it_dispatches_queued_alert_job_when_queue_is_enabled(): void
    {
        Queue::fake();

        config([
            'corewatch.notifications.channels' => ['slack'],
            'corewatch.notifications.queue' => true,
        ]);

        $dispatcher = $this->app->make(AlertDispatcher::class);
        $alerts = [
            'cpu' => new Alert(
                key: 'cpu',
                name: 'CPU Usage',
                current: '95%',
                threshold: '80%',
                severity: AlertSeverity::Critical,
                details: 'High CPU load',
            ),
        ];

        $dispatcher->dispatch($alerts, ['hostname' => 'test-server']);

        Queue::assertPushed(SendQueuedAlertJob::class, function (SendQueuedAlertJob $job) {
            return $job->channels === ['slack']
                && isset($job->alerts['cpu'])
                && $job->systemInfo['hostname'] === 'test-server';
        });
    }

    #[Test]
    public function it_assigns_specified_queue_name_when_string_provided(): void
    {
        Queue::fake();

        config([
            'corewatch.notifications.channels' => ['telegram'],
            'corewatch.notifications.queue' => 'monitoring',
        ]);

        $dispatcher = $this->app->make(AlertDispatcher::class);
        $dispatcher->dispatch([], ['hostname' => 'worker-node']);

        Queue::assertPushed(SendQueuedAlertJob::class, function (SendQueuedAlertJob $job) {
            return $job->queue === 'monitoring';
        });
    }

    #[Test]
    public function it_invokes_notifiers_during_job_handle(): void
    {
        Http::fake([
            'https://hooks.slack.com/*' => Http::response(['ok' => true], 200),
            'https://api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        config([
            'corewatch.notifications.slack.webhook_url' => 'https://hooks.slack.com/services/test/mock/webhook',
            'corewatch.notifications.telegram.bot_token' => '123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11',
            'corewatch.notifications.telegram.chat_id' => '987654321',
        ]);

        $alerts = [
            'ram' => new Alert(
                key: 'ram',
                name: 'RAM Usage',
                current: '92%',
                threshold: '85%',
                severity: AlertSeverity::Warning,
                details: 'High RAM usage',
            ),
        ];
        $systemInfo = [
            'hostname' => 'node-1',
            'os' => 'Linux',
            'php_version' => '8.2.0',
        ];

        $slack = new SlackNotifier;
        $telegram = new TelegramNotifier;

        $job = new SendQueuedAlertJob($alerts, $systemInfo, ['slack', 'telegram']);
        $job->handle($slack, $telegram);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'hooks.slack.com');
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.telegram.org');
        });
    }
}
