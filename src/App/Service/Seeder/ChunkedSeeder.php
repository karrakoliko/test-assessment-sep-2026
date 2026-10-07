<?php

namespace App\App\Service\Seeder;

use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\Seeder\Profile\App\CategorySeedProfile;
use App\App\Service\Seeder\Profile\SeedProfileInterface;

class ChunkedSeeder implements SeederInterface
{
    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected ArticleGateway $articleGateway,
        string $environment,
        public readonly int $chunkSize = 100
    ) {
        if ($environment === 'prod') {
            throw new \Exception('Cannot seed in production environment');
        }
    }

    public function seedCategories(int $n): void
    {

        $profile = new CategorySeedProfile();

        foreach ($this->generateChunks($n, $profile, $this->chunkSize) as $chunk) {
            $this->categoryGateway->insert(...$chunk);
        }

    }

    public function seedArticles(
        int $n,
        Category $category
    ): void {
        // TODO: Implement seedArticles() method.
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
}
