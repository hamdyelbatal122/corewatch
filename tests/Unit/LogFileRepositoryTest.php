<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;
use Hamzi\CoreWatch\Infrastructure\Repositories\LogFileRepository;
use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LogFileRepositoryTest extends TestCase
{
    private LogFileRepository $repository;

    private string $logPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new LogFileRepository;
        $this->logPath = sys_get_temp_dir().'/corewatch_test_'.uniqid().'.log';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->logPath)) {
            unlink($this->logPath);
        }
        parent::tearDown();
    }

    #[Test]
    public function it_parses_laravel_log_lines(): void
    {
        file_put_contents($this->logPath, implode("\n", [
            '[2026-06-05 10:00:00] local.ERROR: Something went wrong',
            '[2026-06-05 10:01:00] local.INFO: All good',
        ]));

        $result = $this->repository->read($this->logPath, 'laravel', 10, 1, new LogFilterDto);

        $this->assertCount(2, $result['logs']);
        // Backward reading returns newest entries first
        $this->assertSame('INFO', $result['logs'][0]['level']);
        $this->assertSame('ERROR', $result['logs'][1]['level']);
    }

    #[Test]
    public function it_filters_logs_by_level(): void
    {
        file_put_contents($this->logPath, implode("\n", [
            '[2026-06-05 10:00:00] local.ERROR: Error message',
            '[2026-06-05 10:01:00] local.INFO: Info message',
        ]));

        $result = $this->repository->read(
            $this->logPath,
            'laravel',
            10,
            1,
            new LogFilterDto(level: 'ERROR')
        );

        $this->assertCount(1, $result['logs']);
        $this->assertSame('ERROR', $result['logs'][0]['level']);
    }

    #[Test]
    public function it_returns_error_for_missing_file(): void
    {
        $result = $this->repository->read('/nonexistent/path.log', 'laravel', 10, 1, new LogFilterDto);

        $this->assertEmpty($result['logs']);
        $this->assertArrayHasKey('error', $result);
    }
}
