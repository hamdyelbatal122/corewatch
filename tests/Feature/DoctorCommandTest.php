<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Feature;

use Hamzi\CoreWatch\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;

final class DoctorCommandTest extends TestCase
{
    #[Test]
    public function test_doctor_command_runs_diagnostics(): void
    {
        $exitCode = Artisan::call('corewatch:doctor');

        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Running CoreWatch System Diagnostics', $output);
        $this->assertStringContainsString('PHP Version', $output);
        $this->assertStringContainsString('Database', $output);
        $this->assertStringContainsString('Cache', $output);
    }
}
