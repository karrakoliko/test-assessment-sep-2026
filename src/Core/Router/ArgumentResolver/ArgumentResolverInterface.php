<?php

namespace App\Core\Router\ArgumentResolver;

use App\Core\Router\Route\RouteInterface;
use Psr\Http\Message\RequestInterface;

interface ArgumentResolverInterface
{
    public function resolveArguments(RequestInterface $request, RouteInterface $route);

}
