<?php

namespace App\App\Service\CategoryManagement\Gateway;

use App\App\Service\CategoryManagement\Category;
use App\App\Service\CategoryManagement\Exception\CategoryNotFoundException;
use App\App\Service\CategoryManagement\ValueObjects\CategoryDescription;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use App\Core\Persistence\Database\TableDataGatewayInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;

class CategoryGateway implements
    TableDataGatewayInterface
{
    public function __construct(
        protected Connection $connection,
    ) {
    }

    /**
     * @return iterable<Category>
     * @throws Exception
     */
    public function findAll(): iterable
    {
        $rows = $this->connection->iterateAssociative(
            <<<'SQL'
                SELECT
                    c.id,
                    c.title,
                    c.description
                FROM categories c
                SQL,
        );

        foreach ($rows as $row) {
            yield new Category(
                title: new CategoryTitle(
                    $row['title']
                ),
                description: new CategoryDescription(
                    $row['description']
                ),
                id: (int)$row['id'],
            );
        }
    }

    /**
     * @return iterable<Category>
     * @throws Exception
     */
    public function findOnesHavingAtLeastNArticles(int $n): iterable
    {
        if ($n < 1) {
            throw new \LogicException(
                'N must be greater than 0'
            );
        }

        $rows = $this->connection->iterateAssociative(
            <<<'SQL'
                SELECT
                    c.id,
                    c.title,
                    c.description
                FROM categories c
                INNER JOIN articles a ON a.category_id = c.id
                GROUP BY
                    c.id,
                    c.title,
                    c.description
                HAVING COUNT(a.id) >= :articles_n
                SQL,
            [
                'articles_n' => $n,
            ],
        );

        foreach ($rows as $row) {
            yield new Category(
                title: new CategoryTitle(
                    $row['title']
                ),
                description: new CategoryDescription(
                    $row['description']
                ),
                id: (int)$row['id'],
            );
        }
    }

    /**
     * @throws Exception
     */
    public function findById(int $id): ?Category
    {
        $row = $this->connection->fetchAssociative(
            <<<'SQL'
                SELECT c.id, c.title, c.description 
                FROM categories c 
                WHERE c.id = :id
                LIMIT 1
                SQL,
            [
                'id' => $id
            ],
            ['id' => ParameterType::INTEGER]
        );

        if ($row === false) {
            return null;
        }

        return new Category(
            title: new CategoryTitle(
                $row['title']
            ),
            description: new CategoryDescription(
                $row['description']
            ),
            id: (int)$row['id'],
        );

    }

    /**
     * @throws CategoryNotFoundException|Exception
     */
    public function findOneByTitle(
        CategoryTitle $title
    ): ?Category {
        $row = $this->connection->fetchAssociative(
            <<<'SQL'
                SELECT
                    c.id,
                    c.title,
                    c.description
                FROM categories c
                WHERE c.title = :title
                LIMIT 1
                SQL,
            [
                'title' => $title->getValue(),
            ],
        );

        if ($row === false) {
            return null;
        }

        return new Category(
            title: new CategoryTitle(
                $row['title']
            ),
            description: new CategoryDescription(
                $row['description']
            ),
            id: (int)$row['id'],
        );
    }

    /**
     * @throws Exception
     */
    public function insert(Category ...$categories): void
    {
        $q = $this->connection->prepare(
            <<<'SQL'
                INSERT INTO categories (
                    title,
                    description
                )
                VALUES (
                    :title,
                    :description
                )
                SQL
        );

        foreach ($categories as $category) {
            $q->bindValue(
                'title',
                $category->getTitle()
            );
            $q->bindValue(
                'description',
                $category->getDescription()
            );
            $q->executeStatement();

            $category->setId(
                (int)$this->connection->lastInsertId()
            );
        }
    }
}
