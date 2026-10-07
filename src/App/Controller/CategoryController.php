<?php

namespace App\App\Controller;

use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use App\App\Service\CategoryManagement\Exception\CategoryNotFoundException;
use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smarty\Smarty;

class CategoryController implements
    ControllerInterface
{
    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected ArticleGateway $articleGateway,
        protected Smarty $smarty
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

            $articles = $this->articleGateway->findLatestArticlesByCategory(
                n: 10,
                category: $category
            );
        } catch (CategoryNotFoundException $e) {
            return new Response(
                status: 404,
                body: 'Category not found'
            );
        }

        $this->smarty->assign(
            'category',
            $category
        );

        $this->smarty->assign(
            'articlesLatest',
            $articles
        );

        $content = $this->smarty->fetch(
            'category.tpl'
        );

        return new Response(
            body: $content
        );
    }

}
