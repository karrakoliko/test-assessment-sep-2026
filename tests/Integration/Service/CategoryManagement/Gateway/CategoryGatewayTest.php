<?php

namespace Tests\Integration\Service\CategoryManagement\Gateway;

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

}
