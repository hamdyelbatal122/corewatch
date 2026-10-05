<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Contracts\ShellExecutorInterface;
use Hamzi\CoreWatch\Domain\Services\SystemDoctor;
use Hamzi\CoreWatch\Tests\TestCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

final class SystemDoctorTest extends TestCase
{
    #[Test]
    public function it_runs_diagnostics_and_returns_structured_results(): void
    {
        $shell = Mockery::mock(ShellExecutorInterface::class);
        $shell->shouldReceive('isDisabled')->andReturn(false);

        $doctor = new SystemDoctor($shell);
        $results = $doctor->runDiagnostics();

        $this->assertNotEmpty($results);

        $validStatuses = ['PASS', 'WARN', 'FAIL'];
        $categories = [];

        foreach ($results as $item) {
            $this->assertArrayHasKey('category', $item);
            $this->assertArrayHasKey('check', $item);
            $this->assertArrayHasKey('status', $item);
            $this->assertArrayHasKey('detail', $item);
            $this->assertContains($item['status'], $validStatuses);
            $categories[$item['category']] = true;
        }

        $this->assertArrayHasKey('System', $categories);
        $this->assertArrayHasKey('Database', $categories);
        $this->assertArrayHasKey('Cache', $categories);
        $this->assertArrayHasKey('Queue', $categories);
        $this->assertArrayHasKey('Scheduler', $categories);
        $this->assertArrayHasKey('Alerts', $categories);
        $this->assertArrayHasKey('Security', $categories);
    }

    #[Test]
    public function it_reports_shell_status_correctly_when_disabled(): void
    {
        $shell = Mockery::mock(ShellExecutorInterface::class);
        $shell->shouldReceive('isDisabled')->andReturn(true);

        $doctor = new SystemDoctor($shell);
        $results = $doctor->runDiagnostics();

        $shellCheck = collect($results)->firstWhere('check', 'Shell Execution');
        $this->assertNotNull($shellCheck);
        $this->assertSame('WARN', $shellCheck['status']);
    }
}
