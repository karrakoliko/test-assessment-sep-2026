<?php

namespace App\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArguments;

class RouteCompiler implements RouteCompilerInterface
{
    /**
     * Compiles a route mask into a regular expression
     * @example /path/{param} => #^/path/(?P<param>[^/]+)$#
     * @see \Tests\Unit\Core\Router\Route\Compiler\RouteCompilerTest
     *
     * @param RouteArguments|array<string, string> $arguments
     */
    public function compile(string $mask, RouteArguments|array $arguments = new RouteArguments()): string
    {
        if (is_array($arguments)) {
            $arguments = RouteArguments::fromArray($arguments);
        }

        $normalizedMask = '/' . trim($mask, '/');

        $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function (array $matches) use ($arguments): string {
            $name = $matches[1];
            $rule = $arguments->get($name)?->getPattern() ?? '[^/]+';

            if (!preg_match('/[+*?]|{\d+,?\d*}$/', $rule)) {
                $rule = "(?:$rule)+";
            }

            return "(?P<$name>$rule)";
        }, $normalizedMask);

        return '#^' . $pattern . '$#';
    }
}
