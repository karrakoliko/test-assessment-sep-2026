<?php

namespace App\Core\Router\UrlGenerator;

use App\Core\Router\Route\Collection\RouteCollection;
use App\Core\Router\Route\Compiler\RouteCompiler;
use App\Core\Router\Route\Compiler\RouteCompilerInterface;
use Uri\Rfc3986\Uri;

class UrlGenerator implements UrlGeneratorInterface
{
    public function __construct(
        protected readonly RouteCollection $routes,
        protected readonly RouteCompilerInterface $routeCompiler = new RouteCompiler()
    ) {
    }

    /**
     * @param array<string, mixed> $arguments
     */
    public function generate(
        string $routeName,
        array $arguments = []
    ): Uri {
        $route = $this->routes->findByName($routeName);
        if ($route === null) {
            throw new \InvalidArgumentException(sprintf('Route "%s" not found.', $routeName));
        }

        return $this->routeCompiler->compileToUrl($route->getMask(), $arguments);
    }
}
