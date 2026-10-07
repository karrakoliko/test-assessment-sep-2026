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
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category(
            title: new CategoryTitle('Tech'),
            description: new CategoryDescription('Tech news'),
            id: 1
        );
    }

    #[Test]
    public function generateReturnsArticleWithPlaceholdCoPicUrl(): void
    {
        $profile = new ArticleSeedProfile([$this->category]);
        $article = $profile->generate();

        self::assertInstanceOf(Article::class, $article);
        self::assertNotEmpty($article->getTitle()->getValue());
        self::assertNotEmpty($article->getContent()->getValue());
        self::assertNotEmpty($article->getDescription()->getValue());
        self::assertStringStartsWith('https://placehold.co/', $article->getPicUrl()->toString());
        self::assertSame($this->category, $article->getCategory());
        self::assertNull($article->getId());
    }

    #[Test]
    public function generateUsesCustomParametersWhenProvided(): void
    {
        $profile = new ArticleSeedProfile([$this->category]);
        $article = $profile->generate([
            'title' => 'Custom Title',
            'content' => 'Custom Content',
            'description' => 'Custom Description',
            'picUrl' => 'https://example.com/test.png',
            'category' => $this->category,
            'id' => 999,
        ]);

        self::assertSame('Custom Title', $article->getTitle()->getValue());
        self::assertSame('Custom Content', $article->getContent()->getValue());
        self::assertSame('Custom Description', $article->getDescription()->getValue());
        self::assertSame('https://example.com/test.png', $article->getPicUrl()->toString());
        self::assertSame($this->category, $article->getCategory());
        self::assertSame(999, $article->getId());
    }

    #[Test]
    public function generateWorksWithGeneratorCategories(): void
    {
        $categoriesGenerator = (function () {
            yield $this->category;
        })();

        $profile = new ArticleSeedProfile($categoriesGenerator);
        $article1 = $profile->generate();
        $article2 = $profile->generate();

        self::assertInstanceOf(Article::class, $article1);
        self::assertInstanceOf(Article::class, $article2);
    }
}
