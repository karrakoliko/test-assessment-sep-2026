<?php

namespace App\App\Controller;

use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Exception\CategoryNotFoundException;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\Core\Router\UrlGenerator\UrlGeneratorInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smarty\Smarty;

class CategoryController implements
    ControllerInterface
{
    public const int ARTICLES_PER_PAGE = 3;

    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected ArticleGateway $articleGateway,
        protected Smarty $smarty,
        protected UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $routeArgs = $request->getAttribute('arguments');
            $categoryId = (int)$routeArgs['id'];

            $category = $this->categoryGateway->findById($categoryId);

            if ($category === null) {
                throw new CategoryNotFoundException();
            }

            // @todo: extract to Paginator service
            $queryParams = $request->getQueryParams();
            $page = max(1, (int)($queryParams['page'] ?? 1));
            $limit = self::ARTICLES_PER_PAGE;
            $offset = ($page - 1) * $limit;

            $articles = iterator_to_array($this->articleGateway->findLatestArticlesByCategory(
                n: $limit,
                category: $category,
                offset: $offset
            ));
        } catch (CategoryNotFoundException $e) {
            return new Response(
                status: 404,
                body: 'Category not found'
            );
        }

        $this->smarty->assign('pageTitle', $category->getTitle()->getValue());

        $this->smarty->assign(
            'category',
            $category
        );

        $this->smarty->assign(
            'articlesLatest',
            $articles
        );

        $this->smarty->assign('paginationPerPage', self::ARTICLES_PER_PAGE);

        $this->smarty->assign(
            'page',
            $page
        );

        $this->smarty->assign('urlGenerator', $this->urlGenerator);

        $content = $this->smarty->fetch(
            'category.tpl'
        );

        return new Response(
            body: $content
        );
    }

}
