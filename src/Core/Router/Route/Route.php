<?php

namespace App\Core\Router\Route;

use App\Core\Router\ControllerReferenceInterface;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Compiler\RouteCompiler;
use App\Core\Router\Route\Compiler\RouteCompilerInterface;
use Psr\Http\Message\ServerRequestInterface;

class Route implements RouteInterface
{
    protected RouteArguments $arguments;
    protected RouteCompilerInterface $routeCompiler;

    /**
     * @param string $mask "/article/{id}-{slug}"
     * @param ControllerReferenceInterface $controllerReference
     * @param RouteArguments|array<string, string> $arguments
     */
    public function __construct(
        protected string $mask,
        protected ControllerReferenceInterface $controllerReference,
        RouteArguments|array $arguments = new RouteArguments()
    ) {
        $this->arguments = is_array($arguments) ? RouteArguments::fromArray($arguments) : $arguments;
        $this->routeCompiler = new RouteCompiler();
    }

    public function getControllerReference(): ControllerReferenceInterface
    {
        return $this->controllerReference;
    }

    /**
     * @see \Tests\Unit\Core\Router\Route\RouteTest
     */
    public function matches(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();

        if (str_starts_with($this->mask, '/^') || str_starts_with($this->mask, '#^') || str_starts_with($this->mask, '~^')) {
            return (bool) @preg_match($this->mask, $path);
        }

        $regex = $this->routeCompiler->compile($this->mask, $this->arguments);

        return (bool) preg_match($regex, '/' . trim($path, '/'));
    }

    public function getArguments(): RouteArguments
    {
        return $this->arguments;
    }

    public function getMask(
    ): string {
        return $this->mask;
    }
}
