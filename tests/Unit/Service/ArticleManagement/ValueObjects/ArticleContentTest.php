<?php

namespace Tests\Unit\Service\ArticleManagement\ValueObjects;

use App\App\Service\ArticleManagement\ValueObjects\ArticleContent;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class ArticleContentTest extends TestCaseUnit
{
    #[Test]
    public function returnsValueAndConvertsToString(): void
    {
        $content = new ArticleContent('Sample Content');

        self::assertSame('Sample Content', $content->getValue());
        self::assertSame('Sample Content', (string) $content);
    }
}
