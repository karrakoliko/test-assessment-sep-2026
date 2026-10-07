<?php

namespace App\App\Service\Seeder\Profile\App;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\ArticleManagement\ValueObjects\ArticleContent;
use App\App\Service\ArticleManagement\ValueObjects\ArticleDescription;
use App\App\Service\ArticleManagement\ValueObjects\ArticleTitle;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\Seeder\Profile\SeedProfileInterface;
use Faker\Factory as FakerFactory;

class ArticleSeedProfile implements SeedProfileInterface
{
    /** @var array<Category> */
    protected array $categories;

    /**
     * @param iterable<Category> $categories - categories to allocate articles to
     */
    public function __construct(iterable $categories)
    {
        $this->categories = is_array($categories) ? $categories : iterator_to_array($categories);
    }

    /**
     * @param array{
     *     title?: string,
     *     content?: string,
     *     description?: string,
     *     picUrl?: string,
     *     category?: Category,
     *     id?: int|null
     * } $params
     * @return Article
     */
    public function generate(array $params = []): Article
    {
        $faker = FakerFactory::create();

        return new Article(
            title: $params['title'] ?? $faker->text(min(255, ArticleTitle::MAX_LENGTH)),
            content: $params['content'] ?? $faker->text(min(2048, ArticleContent::MAX_LENGTH)),
            description: $params['description'] ?? $faker->text(min(255, ArticleDescription::MAX_LENGTH)),
            picUrl: $params['picUrl'] ?? sprintf('https://placehold.co/%dx%d', 600, 400),
            category: $params['category'] ?? $faker->randomElement($this->categories),
            id: $params['id'] ?? null
        );
    }
}
