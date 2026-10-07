<?php

namespace App\App\Service\ArticleManagement\Gateway;

use App\App\Service\ArticleManagement\Article;
use App\App\Service\ArticleManagement\ValueObjects\ArticlePicUrl;
use App\App\Service\CategoryManagement\Category;
use App\Core\Persistence\Database\TableDataGatewayInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;

class ArticleGateway implements
    TableDataGatewayInterface
{
    public function __construct(
        protected Connection $connection,
    ) {
    }

    /**
     * @throws Exception
     */
    public function insert(Article ...$articles): void
    {
        $q = $this->connection->prepare(
            <<<'SQL'
                INSERT INTO articles (
                    title,
                    description,
                    content,
                    pic_url,
                    category_id
                )
                VALUES (
                    :title,
                    :description,
                    :content,
                    :pic_url,
                    :category_id
                )
                SQL
        );

        foreach ($articles as $article) {
            if ($article
                    ->getCategory()
                    ->getId() === null) {
                throw new \LogicException(
                    'Category should be persisted before storing article',
                );
            }

            $q->bindValue(
                'title',
                $article
                    ->getTitle()
                    ->getValue(),
            );
            $q->bindValue(
                'description',
                $article
                    ->getDescription()
                    ->getValue(),
            );
            $q->bindValue(
                'content',
                $article
                    ->getContent()
                    ->getValue(),
            );
            $q->bindValue(
                'pic_url',
                $article
                    ->getPicUrl()
                    ->toString(),
            );
            $q->bindValue(
                'category_id',
                $article
                    ->getCategory()
                    ->getId(),
                ParameterType::INTEGER,
            );

            $q->executeStatement();

            $article->setId(
                (int)$this->connection->lastInsertId(),
            );
        }
    }

    /**
     * @param int $n Latest N entries.
     *
     * @return iterable<Article>
     * @throws Exception
     */
    public function findLatestArticlesByCategory(
        int $n,
        Category $category,
        int $offset = 0
    ): iterable {
        $n = max(
            1,
            $n
        );

        if ($category->getId() === null) {
            throw new \LogicException(
                'Category id is empty',
            );
        }

        $data = $this->connection->iterateAssociative(
            sprintf(
                <<<'SQL'
                    SELECT
                        a.id          AS article_id,
                        a.title       AS article_title,
                        a.content     AS article_content,
                        a.description AS article_description,
                        a.pic_url     AS article_pic_url
                    FROM articles a
                    WHERE a.category_id = :category_id
                    ORDER BY a.created_at DESC, a.id DESC
                    LIMIT %d
                    OFFSET %d
                    SQL,
                $n,
                $offset
            ),
            [
                'category_id' => $category->getId(),
            ],
        );

        foreach ($data as $row) {
            yield new Article(
                title: $row['article_title'],
                content: $row['article_content'],
                description: $row['article_description'],
                picUrl: new ArticlePicUrl(
                    $row['article_pic_url']
                ),
                category: $category,
                id: $row['article_id'],
            );
        }
    }
}
