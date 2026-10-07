<?php

namespace App\App\Service\Seeder\Profile\App;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\App\Service\Seeder\Profile\SeedProfileInterface;
use Faker\Factory as FakerFactory;

class CategorySeedProfile implements SeedProfileInterface
{
    /**
     * @param array{title?: string, description?: string, id?: int} $params
     * @return Category
     */
    public function generate(array $params = []): Category
    {
        $faker = FakerFactory::create();

        return new Category(
            title: $params['title'] ?? $faker->text(CategoryTitle::MAX_LENGTH),
            description: $params['description'] ?? $faker->text(min(1024, CategoryDescription::MAX_LENGTH)),
            id: $params['id'] ?? null,
        );

    }
}
