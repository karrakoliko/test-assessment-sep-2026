<?php

namespace App\Core\Router\Route\Collection;

use App\Core\Router\Route\RouteInterface;
use Traversable;

class RouteCollection implements \IteratorAggregate
{
    /**
     * @param array<RouteInterface> $routes
     */
    public function __construct(protected array $routes = [])
    {

    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator((array)$this->routes);
    }

    public function findByName(string $name): ?RouteInterface
    {
        foreach ($this->routes as $route) {
            if ($route->getName() !== $name) {
                continue;
            }

            return $route;
        }

        return null;
    }
}
