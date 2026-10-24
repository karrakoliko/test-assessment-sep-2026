<?php

namespace Integration\App\Service\ArticleManagement\Gateway;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\TestCaseIntegration;

class ArticleGatewayTest extends TestCaseIntegration
{
    #[Test]
    public function findLatestArticlesByCategory()
    {
        /** @var Connection $conn */
        $conn = self::$container->get(Connection::class);

        $categoryGateway = new CategoryGateway(connection: $conn);
        $articleGateway = new ArticleGateway(connection: $conn);

        $c1 = new Category(
            title: new CategoryTitle(title: 'category1'),
            description: new CategoryDescription(description: 'desc1'),
        );

        $a11 = new Article(
            title: 'title1',
            content: 'content1',
            description: 'desc1',
            picUrl: 'https://example1.com/image1.jpg',
            category: $c1
        );

        $a12 = new Article(
            title: 'title2',
            content: 'content2',
            description: 'desc2',
            picUrl: 'https://example1.com/image2.jpg',
            category: $c1
        );

        $a13 = new Article(
            title: 'title3',
            content: 'content3',
            description: 'desc3',
            picUrl: 'https://example1.com/image3.jpg',
            category: $c1
        );

        $c2 = new Category(
            title: new CategoryTitle(title: 'category2'),
            description: new CategoryDescription(description: 'desc2'),
        );

        $conn->transactional(function () use ($categoryGateway, $articleGateway, $c1, $c2, $a11, $a12, $a13) {
            $categoryGateway->insert($c1, $c2);
            $articleGateway->insert($a11);
            usleep(2000); // delay needed for proper "latest" ordering (e.g. "created_at" population)
            $articleGateway->insert($a12);
            usleep(2000); // delay needed for proper "latest" ordering (e.g. "created_at" population)
            $articleGateway->insert($a13);
        });

        $found = iterator_to_array($articleGateway->findLatestArticlesByCategory(2, $c1));

        $foundIds = array_map(function (Article $article) {
            return $article->getId();
        }, $found);

        self::assertCount(2, $foundIds);
        self::assertNotContains($a11->getId(), $foundIds, 'a11 should not present in result bc it is the oldest');
        self::assertContains($a12->getId(), $foundIds, 'a12 is missing');
        self::assertContains($a13->getId(), $foundIds, 'a13 is missing');

    }

    #[Test]
    public function findById()
    {
        /** @var Connection $conn */
        $conn = self::$container->get(Connection::class);

        $categoryGateway = new CategoryGateway(connection: $conn);
        $articleGateway = new ArticleGateway(connection: $conn);

        $category = new Category(
            title: new CategoryTitle(title: 'cat_test'),
            description: new CategoryDescription(description: 'desc_test'),
        );

        $article = new Article(
            title: 'article_title',
            content: 'article_content',
            description: 'article_desc',
            picUrl: 'https://example.com/pic.jpg',
            category: $category
        );

        $conn->transactional(function () use ($categoryGateway, $articleGateway, $category, $article) {
            $categoryGateway->insert($category);
            $articleGateway->insert($article);
        });

        $found = $articleGateway->findById($article->getId());

        self::assertNotNull($found);
        self::assertSame($article->getId(), $found->getId());
        self::assertSame('article_title', $found->getTitle()->getValue());
        self::assertSame('article_content', $found->getContent()->getValue());
        self::assertSame('article_desc', $found->getDescription()->getValue());
        self::assertSame('https://example.com/pic.jpg', $found->getPicUrl()->toString());
        self::assertNotNull($found->getCategory());
        self::assertSame($category->getId(), $found->getCategory()->getId());
        self::assertSame('cat_test', $found->getCategory()->getTitle()->getValue());
        self::assertSame('desc_test', $found->getCategory()->getDescription()->getValue());

        $notFound = $articleGateway->findById(999999);
        self::assertNull($notFound);
    }
}
