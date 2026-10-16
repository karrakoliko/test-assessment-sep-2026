<?php

namespace App\Core\Router\UrlGenerator;

use App\Core\Router\Route\Collection\RouteCollection;
use App\Core\Router\Route\Compiler\RouteCompiler;
use App\Core\Router\Route\Compiler\RouteCompilerInterface;
use Uri\InvalidUriException;
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
     * @param array<string, mixed> $queryParams
     * @throws InvalidUriException
     */
    public function generate(
        string $routeName,
        array $arguments = [],
        array $queryParams = []
    ): Uri {
        $route = $this->routes->findByName($routeName);
        if ($route === null) {
            throw new \InvalidArgumentException(sprintf('Route "%s" not found.', $routeName));
        }

        $uri = $this->routeCompiler->compileToUrl(
            $route->getMask(),
            $arguments
        );

        if (count($queryParams) > 0) {
            $uri = $uri->withQuery(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
        }

        return $uri;
    }
}
