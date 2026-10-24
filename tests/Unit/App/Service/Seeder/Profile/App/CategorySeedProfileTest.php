<?php

namespace Tests\Unit\App\Service\Seeder\Profile\App;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\Seeder\Profile\App\CategorySeedProfile;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class CategorySeedProfileTest extends TestCaseUnit
{
    #[Test]
    public function generateReturnsCategoryWithFakerValues(): void
    {
        $profile = new CategorySeedProfile(faker: \Faker\Factory::create());
        $category = $profile->generate();

        self::assertInstanceOf(Category::class, $category);
        self::assertNotEmpty($category->getTitle()->getValue());
        self::assertNotEmpty($category->getDescription()->getValue());
        self::assertNull($category->getId());
    }

    #[Test]
    public function generateUsesCustomParametersWhenProvided(): void
    {
        $profile = new CategorySeedProfile(faker: \Faker\Factory::create());
        $category = $profile->generate([
            'title' => 'Custom Category',
            'description' => 'Custom Description',
            'id' => 123,
        ]);

        self::assertSame('Custom Category', $category->getTitle()->getValue());
        self::assertSame('Custom Description', $category->getDescription()->getValue());
        self::assertSame(123, $category->getId());
    }

}
