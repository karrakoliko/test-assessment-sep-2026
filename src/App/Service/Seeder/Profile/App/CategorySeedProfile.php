<?php

namespace App\App\Service\Seeder\Profile\App;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\App\Service\Seeder\Profile\SeedProfileInterface;

class CategorySeedProfile implements SeedProfileInterface
{
    public function __construct(protected \Faker\Generator $faker)
    {
    }
    /**
     * @param array{title?: string, description?: string, id?: int} $params
     * @return Category
     */
    public function generate(array $params = []): Category
    {
        return new Category(
            title: $params['title'] ?? $this->faker->text(CategoryTitle::MAX_LENGTH),
            description: $params['description'] ?? $this->faker->text(min(1024, CategoryDescription::MAX_LENGTH)),
            id: $params['id'] ?? null,
        );

    }
}
