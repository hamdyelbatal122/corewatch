<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Application\Actions\ExecuteServiceCommandAction;
use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ExecuteServiceCommandActionTest extends TestCase
{
    #[Test]
    public function it_rejects_disabled_service_commands(): void
    {
        config(['corewatch.services.redis_flush.enabled' => false]);

        $action = $this->app->make(ExecuteServiceCommandAction::class);

        $result = $action->execute('redis_flush');

        $this->assertFalse($result['success']);
        $this->assertTrue($result['disabled']);
    }

    #[Test]
    public function it_rejects_unregistered_service_keys(): void
    {
        $action = $this->app->make(ExecuteServiceCommandAction::class);

        $result = $action->execute('invalid_key_xyz');

        $this->assertFalse($result['success']);
        $this->assertTrue($result['not_found']);
    }
}
