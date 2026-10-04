<?php

namespace App\Core\Router;

use App\Core\Router\Route\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

interface RouterInterface
{
    public function match(ServerRequestInterface $request): ?RouteInterface;

}
