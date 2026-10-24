<?php

namespace Tests\Unit\App\Service\CategoryManagement\ValueObjects;

use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class CategoryDescriptionTest extends TestCaseUnit
{
    #[Test]
    public function returnsValueAndConvertsToString(): void
    {
        $description = new CategoryDescription('Sample Description');

        self::assertSame('Sample Description', $description->getValue());
        self::assertSame('Sample Description', (string) $description);
    }
}
