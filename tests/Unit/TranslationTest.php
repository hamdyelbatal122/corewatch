<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Tests\Unit;

use Hamzi\CoreWatch\Support\Translation;
use Hamzi\CoreWatch\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class TranslationTest extends TestCase
{
    #[Test]
    public function it_returns_english_fallback_when_lang_files_missing(): void
    {
        $this->assertSame('COREWATCH', Translation::get('title'));
        $this->assertSame('Operations Insights', Translation::get('ops_insights'));
    }

    #[Test]
    public function it_returns_all_labels_for_frontend_config(): void
    {
        $labels = Translation::all();

        $this->assertArrayHasKey('title', $labels);
        $this->assertArrayHasKey('polling_active', $labels);
        $this->assertNotSame('corewatch::title', $labels['title']);
    }
}
