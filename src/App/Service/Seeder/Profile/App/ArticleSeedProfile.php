<?php

namespace App\App\Service\Seeder\Profile\App;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\ArticleManagement\ValueObjects\ArticleContent;
use App\App\Service\ArticleManagement\ValueObjects\ArticleDescription;
use App\App\Service\ArticleManagement\ValueObjects\ArticleTitle;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\Seeder\Profile\SeedProfileInterface;
use Faker\Factory as FakerFactory;
use InvalidArgumentException;
use Random\RandomException;

class ArticleSeedProfile implements SeedProfileInterface
{
    /** @var iterable<Category> */
    protected array $categoriesReservoir = [];

    /**
     * @param iterable<Category> $categories Categories to allocate articles to
     * @param int $reservoirSize Maximum number of categories to seed articles to
     * @throws RandomException
     */
    public function __construct(
        iterable $categories,
        protected int $reservoirSize = 10,
    ) {
        if ($reservoirSize < 1) {
            throw new InvalidArgumentException(
                '$reservoirSize must be greater than zero.',
            );
        }

        $this->fillCategoriesReservoir($categories);
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
     */
    public function generate(array $params = []): Article
    {
        $faker = FakerFactory::create();

        return new Article(
            title: $params['title'] ?? $faker->text(min(255, ArticleTitle::MAX_LENGTH)),
            content: $params['content'] ?? $faker->text(min(2048, ArticleContent::MAX_LENGTH)),
            description: $params['description'] ?? $faker->text(min(255, ArticleDescription::MAX_LENGTH)),
            picUrl: $params['picUrl'] ?? sprintf('https://placehold.co/%dx%d', 600, 400),
            category: $params['category'] ?? $faker->randomElement($this->categoriesReservoir),
            id: $params['id'] ?? null,
        );
    }

    /**
     *  Selects a uniformly random sample of categories using reservoir sampling.
     *
     *  Retains the first k categories, then considers each subsequent category
     *  with probability k/n, replacing a random reservoir entry if selected.
     *
     *  Uses O(k) memory without knowing the total number of categories in advance.
     *
     * @param iterable<Category> $categories
     * @return void
     * @throws RandomException
     *
     * @link https://en.wikipedia.org/wiki/Reservoir_sampling
     * /
     */
    protected function fillCategoriesReservoir(iterable $categories): void
    {
        $count = 0;

        foreach ($categories as $category) {
            ++$count;

            if ($count <= $this->reservoirSize) {
                $this->categoriesReservoir[] = $category;

                continue;
            }

            $index = random_int(1, $count);

            if ($index <= $this->reservoirSize) {
                $this->categoriesReservoir[$index - 1] = $category;
            }
        }

        if ($count < 1) {
            throw new \RuntimeException(
                'Cannot generate an article without available categories.',
            );
        }
    }
}
