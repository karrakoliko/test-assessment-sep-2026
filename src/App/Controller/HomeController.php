<?php

namespace App\App\Controller;

use Nyholm\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Smarty\Smarty;

class HomeController implements ControllerInterface
{
    public function __construct(
        protected Smarty $smarty
    ) {

    }

    public function index(RequestInterface $request): ResponseInterface
    {
        return new Response(
            body: $this->smarty->fetch('home.tpl')
        );
    }

}
