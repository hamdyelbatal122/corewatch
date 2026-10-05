<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Feature;

use Hamzi\CoreWatch\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

final class TestAlertCommandTest extends TestCase
{
    #[Test]
    public function test_test_alert_command_dispatches_successfully(): void
    {
        config(['corewatch.notifications.channels' => ['slack']]);
        config(['corewatch.notifications.slack.webhook_url' => 'https://hooks.slack.com/services/test']);

        $exitCode = Artisan::call('corewatch:test-alert');

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Test alert successfully dispatched', $output);
    }

    #[Test]
    public function test_test_alert_command_warns_when_no_channels_enabled(): void
    {
        config(['corewatch.notifications.channels' => []]);

        $exitCode = Artisan::call('corewatch:test-alert');

        $this->assertEquals(1, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('No notification channels are enabled', $output);
    }
}
