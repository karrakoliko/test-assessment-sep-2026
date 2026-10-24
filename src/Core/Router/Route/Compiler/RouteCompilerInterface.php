<?php

namespace App\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArguments;
use Uri\Rfc3986\Uri;

interface RouteCompilerInterface
{
    /**
     * @param RouteArguments|array<string, string> $arguments
     */
    public function compileToRegex(string $mask, RouteArguments|array $arguments = new RouteArguments()): string;

    /**
     * @param array<string, mixed> $arguments
     */
    public function compileToUri(string $mask, array $arguments = []): Uri;
}
