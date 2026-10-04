<?php

namespace App\App\Controller;

use App\App\Service\CategoryManagement\Gateway\CategoryGateway;
use App\App\Service\CategoryManagement\Gateway\Exception\CategoryNotFoundException;
use App\App\Service\CategoryManagement\ValueObjects\CategoryTitle;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Smarty\Smarty;

class CategoryController implements ControllerInterface
{
    public function __construct(
        protected CategoryGateway $categoryGateway,
        protected Smarty $smarty
    ) {
    }

    public function show(ServerRequestInterface $request): ResponseInterface
    {

        try {
            $category = $this->categoryGateway->findOneByTitle(new CategoryTitle('test1'));
        } catch (CategoryNotFoundException $e) {
            return new Response(status: 404, body: 'Category not found');
        }

        $this->smarty->assign('name', $category?->getTitle());

        $content = $this->smarty->fetch('category.tpl');

        return new Response(body: $content);

    }

}
