<?php

namespace App\App\Service\HomePage;

use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\HomePage\Dto\HomePageCategoryDto;
use App\Core\Router\UrlGenerator\UrlGeneratorInterface;
use Doctrine\DBAL\Exception;

class HomePageService
{
    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected ArticleGateway $articleGateway,
        protected UrlGeneratorInterface $urlGenerator
    ) {
    }

    /**
     * @return iterable<HomePageCategoryDto>
     * @throws Exception
     */
    public function getHomePageCategories(): iterable
    {
        $categories = $this->categoryGateway->findOnesHavingAtLeastNArticles(1);

        foreach ($categories as $category) {

            $articlesLatest = $this->articleGateway->findLatestArticlesByCategory(3, $category);

            yield new HomePageCategoryDto(
                category: $category,
                articlesLatest: $articlesLatest,
            );
        }

    }

}
