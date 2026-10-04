<?php

namespace App\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArguments;

interface RouteCompilerInterface
{
    /**
     * @param RouteArguments|array<string, string> $arguments
     */
    public function compile(string $mask, RouteArguments|array $arguments = new RouteArguments()): string;
}
