<?php

namespace App\App\Controller;

use App\App\Service\ArticleManagement\Exception\ArticleNotFoundException;
use App\App\Service\ArticleManagement\Gateway\ArticleGateway;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smarty\Smarty;

class ArticleController implements
    ControllerInterface
{
    public function __construct(
        protected ArticleGateway $articleGateway,
        protected Smarty $smarty
    ) {
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $routeArgs = $request->getAttribute('arguments');
            $articleId = (int)$routeArgs['id'];

            $article = $this->articleGateway->findById($articleId);

            if ($article === null) {
                throw new ArticleNotFoundException();
            }
        } catch (ArticleNotFoundException $e) {
            return new Response(
                status: 404,
                body: 'Article not found'
            );
        }

        $this->smarty->assign('pageTitle', $article->getTitle()->getValue());

        $this->smarty->assign(
            'article',
            $article
        );

        $content = $this->smarty->fetch(
            'article.tpl'
        );

        return new Response(
            body: $content
        );
    }

}
