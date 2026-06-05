<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Feature;

use Hamzi\CoreWatch\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

final class InstallCommandTest extends TestCase
{
    #[Test]
    public function test_install_command_runs_successfully(): void
    {
        $exitCode = Artisan::call('corewatch:install');

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('CoreWatch installed successfully', Artisan::output());
    }
}
