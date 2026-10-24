<?php

namespace Tests\Unit\App\Service\CategoryManagement\ValueObjects;

use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CategoryNameTest extends
    TestCase
{
    #[Test]
    public function throwsIfLongerThanDbLimit()
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Category title is too long');

        new CategoryTitle(
            title: str_repeat('a', CategoryTitle::MAX_LENGTH + 1)
        );

    }

}
