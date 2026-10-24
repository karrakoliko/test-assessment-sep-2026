<?php

namespace App\App\Controller;

use App\App\Service\HomePage\HomePageService;
use App\Core\Router\UriGenerator\UriGeneratorInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smarty\Smarty;

class HomeController implements ControllerInterface
{
    public function __construct(
        protected Smarty $smarty,
        protected HomePageService $homePageService,
        protected UriGeneratorInterface $uriGenerator
    ) {

    }

    public function index(RequestInterface $request): ResponseInterface
    {
        $this->smarty->assign('homePageCategories', $this->homePageService->getHomePageCategories());
        $this->smarty->assign('uriGenerator', $this->uriGenerator);
        return new Response(
            body: $this->smarty->fetch('home.tpl')
        );
    }

}
