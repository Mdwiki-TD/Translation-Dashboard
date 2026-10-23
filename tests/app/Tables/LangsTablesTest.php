<?php

declare(strict_types=1);

namespace Tests\App\Tables;

use App\Tables\LangsTables;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LangsTables::class)]
class LangsTablesTest extends TestCase
{
    public function testLangsTablesLookups(): void
    {
        $title = LangsTables::get_lang_title('ar');
        $name = LangsTables::get_lang_name('ar');

        if ($title !== null) {
            $this->assertStringContainsString('ar', $title);
        } else {
            $this->assertNull($title);
        }

        $this->assertTrue($name === null || is_string($name));
        $this->assertContains('commons', LangsTables::$L_skip_codes);
    }
}
