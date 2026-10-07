<?php

namespace App\Core\Router;

use App\Core\Router\Route\Collection\RouteCollection;
use App\Core\Router\Route\RouteInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router implements RouterInterface
{
    /**
     * @param RouteCollection $routes
     */
    public function __construct(protected readonly RouteCollection $routes)
    {

    }

    public function match(ServerRequestInterface $request): ?RouteInterface
    {

        foreach ($this->routes as $route) {
            if (!$route->matches($request)) {
                continue;
            }

            return $route;
        }

        return null;
    }
}
