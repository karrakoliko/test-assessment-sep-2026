<?php

namespace Tests\Unit\App\Service\ArticleManagement\ValueObjects;

use App\App\Service\ArticleManagement\ValueObjects\ArticleDescription;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class ArticleDescriptionTest extends TestCaseUnit
{
    #[Test]
    public function returnsValueAndConvertsToString(): void
    {
        $description = new ArticleDescription('Sample Description');

        self::assertSame('Sample Description', $description->getValue());
        self::assertSame('Sample Description', (string) $description);
    }
}
