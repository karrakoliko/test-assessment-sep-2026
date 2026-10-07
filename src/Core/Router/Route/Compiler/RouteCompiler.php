<?php

namespace App\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArguments;
use Uri\Rfc3986\Uri;

class RouteCompiler implements RouteCompilerInterface
{
    /**
     * Compiles a route mask into a regular expression
     * @example /path/{param} => #^/path/(?P<param>[^/]+)$#
     * @see \Tests\Unit\Core\Router\Route\Compiler\RouteCompilerTest
     *
     * @param RouteArguments|array<string, string> $arguments
     */
    public function compileToRegex(string $mask, RouteArguments|array $arguments = new RouteArguments()): string
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

    /**
     * Compiles a route mask into a Uri by replacing placeholders with given arguments
     * @example /path/{param} with ['param' => 'value'] => /path/value
     *
     * @param array<string, mixed> $arguments
     */
    public function compileToUrl(string $mask, array $arguments = []): Uri
    {
        $normalizedMask = '/' . trim($mask, '/');

        $url = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function (array $matches) use ($arguments, $mask): string {
            $name = $matches[1];
            if (!array_key_exists($name, $arguments)) {
                throw new \InvalidArgumentException(sprintf('Missing required argument "%s" for route mask "%s".', $name, $mask));
            }

            return (string) $arguments[$name];
        }, $normalizedMask);

        return new Uri($url);
    }
}
