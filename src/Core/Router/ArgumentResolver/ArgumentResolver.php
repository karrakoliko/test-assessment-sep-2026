<?php

namespace App\Core\Router\ArgumentResolver;

use App\Core\Router\Route\Compiler\RouteCompiler;
use App\Core\Router\Route\Compiler\RouteCompilerInterface;
use App\Core\Router\Route\RouteInterface;
use Psr\Http\Message\RequestInterface;

final class ArgumentResolver implements ArgumentResolverInterface
{
    public function __construct(
        private readonly ?RouteCompilerInterface $routeCompiler = new RouteCompiler()
    ) {
    }

    /**
     * @see \Tests\Unit\Core\Router\ArgumentResolver\ArgumentResolverTest
     */
    public function resolveArguments(
        RequestInterface $request,
        RouteInterface $route
    ): array {
        $mask = '/' . trim($route->getMask(), '/');
        if (!str_contains($mask, '{')) {
            return [];
        }

        $regex = $this->routeCompiler->compile($mask, $route->getArguments());

        if (preg_match($regex, '/' . trim($request->getUri()->getPath(), '/'), $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }

        return [];
    }
}
