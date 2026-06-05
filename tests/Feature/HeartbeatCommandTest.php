<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Feature;

use Hamzi\CoreWatch\Infrastructure\Collectors\ScheduleHeartbeatCollector;
use Hamzi\CoreWatch\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

final class HeartbeatCommandTest extends TestCase
{
    #[Test]
    public function test_heartbeat_command_records_schedule_status(): void
    {
        Artisan::call('corewatch:heartbeat');

        $collector = $this->app->make(ScheduleHeartbeatCollector::class);
        $status = $collector->collect();

        $this->assertTrue($status['active']);
        $this->assertSame('Scheduler Active ✅', $status['status']);
    }
}
