<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Support\PackageVersion;
use Hamzi\CoreWatch\Tests\TestCase;

class PackageVersionTest extends TestCase
{
    public function test_current_returns_non_empty_string(): void
    {
        $version = PackageVersion::current();

        $this->assertNotSame('', $version);
        $this->assertIsString($version);
    }
}
