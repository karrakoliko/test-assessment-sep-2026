<?php

namespace Tests\Unit\Service\ArticleManagement\ValueObjects;

use App\App\Service\ArticleManagement\ValueObjects\ArticleTitle;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class ArticleTitleTest extends TestCaseUnit
{
    #[Test]
    public function throwsIfLongerThanDbLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Article title is too long');

        new ArticleTitle(title: str_repeat('a', ArticleTitle::MAX_LENGTH + 1));
    }

    #[Test]
    public function returnsValueAndConvertsToString(): void
    {
        $title = new ArticleTitle('Sample Title');

        self::assertSame('Sample Title', $title->getValue());
        self::assertSame('Sample Title', (string) $title);
    }
}
