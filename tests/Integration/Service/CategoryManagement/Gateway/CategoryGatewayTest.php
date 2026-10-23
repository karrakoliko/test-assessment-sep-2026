<?php

namespace Tests\Integration\Service\CategoryManagement\Gateway;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\TestCaseIntegration;

class CategoryGatewayTest extends TestCaseIntegration
{
    #[Test]
    public function findOnesHavingAtLeastOneArticle(): void
    {

        /** @var Connection $conn */
        $conn = self::$container->get(Connection::class);

        $categoryGateway = new CategoryGateway(connection: $conn);

        $c1 = new Category(
            title: new CategoryTitle(title: 'category1'),
            description: new CategoryDescription(description: 'desc1'),
        );

        $c2 = new Category(
            title: new CategoryTitle(title: 'category2'),
            description: new CategoryDescription(description: 'desc2'),
        );

        $conn->transactional(function () use ($categoryGateway, $c1, $c2) {
            $categoryGateway->insert($c1, $c2);
        });

        $a1 = new Article(
            title: 'title1',
            content: 'content1',
            description: 'desc1',
            picUrl: 'https://example1.com/image1.jpg',
            category: $c1
        );

        $articleGateway = new ArticleGateway(connection: $conn);
        $articleGateway->insert($a1);

        $foundCategories = [];

        foreach ($categoryGateway->findOnesHavingAtLeastNArticles(1) as $category) {
            $foundCategories[] = $category;
        }

        $this->assertCount(1, $foundCategories);
        $this->assertEquals($c1, $foundCategories[0]);
    }

    #[Test]
    public function insertFailsIfCategoryWithGivenNameAlreadyExists(): void
    {
        /** @var Connection $conn */
        $conn = self::$container->get(Connection::class);

        $gw = new CategoryGateway(connection: $conn);

        $name = new CategoryTitle(
            title: 'test1'
        );

        $category = new Category(
            title: $name,
            description: new CategoryDescription(description: 'desc1'),
        );

        $gw->insert($category);

        $this->expectException(UniqueConstraintViolationException::class);
        $this->expectExceptionMessageIsOrContains(sprintf("Duplicate entry '%s'", $name->getValue()));

        $gw->insert($category);

    }

    #[Test]
    public function insertStoresRow()
    {

        $gw = new CategoryGateway(connection: self::$container->get(Connection::class));

        $name = new CategoryTitle(
            title: 'test1'
        );

        $category = new Category(
            title: $name,
            description: new CategoryDescription(description: 'desc1'),
        );

        $gw->insert($category);

        self::assertNotNull($gw->findOneByTitle($name));

    }

    #[Test]
    public function countReturnsTotalNumberOfCategories(): void
    {
        /** @var Connection $conn */
        $conn = self::$container->get(Connection::class);

        $gw = new CategoryGateway(connection: $conn);

        self::assertSame(0, $gw->count());

        $c1 = new Category(
            title: new CategoryTitle(title: 'category1'),
            description: new CategoryDescription(description: 'desc1'),
        );

        $c2 = new Category(
            title: new CategoryTitle(title: 'category2'),
            description: new CategoryDescription(description: 'desc2'),
        );

        $conn->transactional(function () use ($gw, $c1, $c2) {
            $gw->insert($c1, $c2);
        });

        self::assertSame(2, $gw->count());
    }
}
