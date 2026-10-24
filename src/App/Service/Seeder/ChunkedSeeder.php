<?php

namespace App\App\Service\Seeder;

use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\Seeder\Profile\App\ArticleSeedProfile;
use App\App\Service\Seeder\Profile\App\CategorySeedProfile;
use App\App\Service\Seeder\Profile\SeedProfileInterface;

class ChunkedSeeder implements SeederInterface
{
    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected ArticleGateway $articleGateway,
        protected \Faker\Generator $faker,
        string $environment,
        public readonly int $chunkSize = 100
    ) {
        if ($environment === 'prod') {
            throw new \Exception('Cannot seed in production environment');
        }
    }

    public function seedCategories(int $n): void
    {

        $profile = new CategorySeedProfile(
            faker: $this->faker,
        );

        foreach ($this->generateChunks($n, $profile, $this->chunkSize) as $chunk) {
            $this->categoryGateway->insert(...$chunk);
        }

    }

    public function seedArticlesAmongExistingCategoriesRandomly(
        int $n
    ): void {

        $profile = new ArticleSeedProfile(
            categories: $this->categoryGateway->findAll(),
            faker: $this->faker,
            reservoirSize: min($this->categoryGateway->count(), $this->chunkSize),
        );

        foreach ($this->generateChunks($n, $profile, $this->chunkSize) as $chunk) {
            $this->articleGateway->insert(...$chunk);
        }
    }


    protected function generateChunks(int $n, SeedProfileInterface $profile, int $chunkSize): \Generator
    {
        $chunk = [];

        for ($i = 0; $i < $n; ++$i) {
            $chunk[] = $profile->generate();

            if (count($chunk) === $chunkSize) {
                yield $chunk;
                $chunk = [];
            }
        }

        if ($chunk !== []) {
            yield $chunk;
        }
    }

    public function seedArticlesToCategory(
        int $n,
        Category $category
    ): void {
        $profile = new ArticleSeedProfile(
            categories: [$category],
            faker: $this->faker,
        );

        foreach ($this->generateChunks($n, $profile, $this->chunkSize) as $chunk) {
            $this->articleGateway->insert(...$chunk);
        }
    }
}
