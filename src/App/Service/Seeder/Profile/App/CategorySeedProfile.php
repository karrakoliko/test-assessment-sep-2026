<?php

namespace App\App\Service\Seeder\Profile\App;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\App\Service\Seeder\Profile\SeedProfileInterface;
use Faker\Factory as FakerFactory;

class CategorySeedProfile implements SeedProfileInterface
{
    protected \Faker\Generator $faker;

    public function __construct()
    {

        $this->faker = FakerFactory::create();
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
