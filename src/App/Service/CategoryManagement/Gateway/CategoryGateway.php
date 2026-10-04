<?php

namespace App\App\Service\CategoryManagement\Gateway;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Gateway\Exception\CategoryNotFoundException;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\Core\Persistence\Database\TableDataGatewayInterface;
use Doctrine\DBAL\Connection;

class CategoryGateway implements TableDataGatewayInterface
{
    public function __construct(protected Connection $connection)
    {

    }

    /**
     * @return iterable<Category>
     */
    public function findHavingAtLeastOneArticle(): iterable
    {
        $qb = $this->connection->createQueryBuilder();

        $rows = $qb
            ->select('c.title', 'c.description')
            ->from('categories', 'c')
            ->innerJoin('c', 'articles', 'a', 'a.category_id = c.id')
            ->distinct()
            ->executeQuery()
            ->iterateAssociative();

        foreach ($rows as $row) {
            yield new Category(
                title: new CategoryTitle($row['title']),
                description: new CategoryDescription($row['description']),
            );
        }
    }

    public function findOneByTitle(CategoryTitle $title): ?Category
    {

        $qb = $this->connection->createQueryBuilder();

        $q = $qb
            ->select('c.title', 'c.description')
            ->from('categories', 'c')
            ->where('title = :title')
            ->setParameter('title', $title->getValue())
            ->setMaxResults(1)
        ;

        $data = $q->executeQuery()->fetchAssociative();

        if ($data === false) {
            throw new CategoryNotFoundException();
        }

        return new Category(
            title: new CategoryTitle($data['title']),
            description: new CategoryDescription($data['description']),
        );

    }

    public function insert(Category $category): void
    {
        $q = $this->connection->prepare('insert into categories (title, description) values (:title, :description)');

        $q->bindValue('title', $category->getTitle());
        $q->bindValue('description', $category->getDescription());
        $q->executeQuery();
    }

}
