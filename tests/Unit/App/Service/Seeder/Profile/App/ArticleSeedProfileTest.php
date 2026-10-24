<?php

namespace Tests\Unit\App\Service\Seeder\Profile\App;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\App\Service\Seeder\Profile\App\ArticleSeedProfile;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class ArticleSeedProfileTest extends TestCaseUnit
{
    #[Test]
    public function generateReturnsArticle(): void
    {
        $category = new Category(
            title: new CategoryTitle('Tech'),
            description: new CategoryDescription('Tech news'),
            id: 1
        );

        $profile = new ArticleSeedProfile(categories: [$category], faker: \Faker\Factory::create());
        $article = $profile->generate();

        self::assertInstanceOf(Article::class, $article);
        self::assertNotEmpty($article->getTitle()->getValue());
        self::assertNotEmpty($article->getContent()->getValue());
        self::assertNotEmpty($article->getDescription()->getValue());
        self::assertStringStartsWith('https://placehold.co/', $article->getPicUri()->toString());
        self::assertSame($category, $article->getCategory());
        self::assertNull($article->getId());
    }

    #[Test]
    public function generateUsesCustomParametersWhenProvided(): void
    {
        $category = new Category(
            title: new CategoryTitle('Tech'),
            description: new CategoryDescription('Tech news'),
            id: 1
        );

        $profile = new ArticleSeedProfile(categories: [$category], faker: \Faker\Factory::create());
        $article = $profile->generate([
            'title' => 'Custom Title',
            'content' => 'Custom Content',
            'description' => 'Custom Description',
            'picUri' => 'https://example.com/test.png',
            'category' => $category,
            'id' => 999,
        ]);

        self::assertSame('Custom Title', $article->getTitle()->getValue());
        self::assertSame('Custom Content', $article->getContent()->getValue());
        self::assertSame('Custom Description', $article->getDescription()->getValue());
        self::assertSame('https://example.com/test.png', $article->getPicUri()->toString());
        self::assertSame($category, $article->getCategory());
        self::assertSame(999, $article->getId());
    }

    #[Test]
    public function generateWorksWithGeneratorCategories(): void
    {
        $categoriesGenerator = (function () {
            yield new Category(
                title: new CategoryTitle('Tech'),
                description: new CategoryDescription('Tech news'),
                id: 1
            );
        })();

        $profile = new ArticleSeedProfile(categories: $categoriesGenerator, faker: \Faker\Factory::create());
        $article1 = $profile->generate();
        $article2 = $profile->generate();

        self::assertInstanceOf(Article::class, $article1);
        self::assertInstanceOf(Article::class, $article2);
    }
}
