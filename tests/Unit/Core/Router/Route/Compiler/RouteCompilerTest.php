<?php

namespace Tests\Unit\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Compiler\RouteCompiler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteCompilerTest extends TestCase
{
    #[Test]
    #[DataProvider('provideCompilationCases')]
    public function compile(string $mask, RouteArguments|array $arguments, string $expectedRegex): void
    {
        $compiler = new RouteCompiler();

        self::assertSame($expectedRegex, $compiler->compile($mask, $arguments));
    }

    public static function provideCompilationCases(): iterable
    {
        yield 'root slash' => ['/', [], '#^/$#'];
        yield 'empty string' => ['', [], '#^/$#'];
        yield 'static path with trailing slash' => ['posts/all/', [], '#^/posts/all$#'];
        yield 'static path' => ['/posts/all', [], '#^/posts/all$#'];
        yield 'single placeholder with leading slash' => ['/category/{id}', [], '#^/category/(?P<id>[^/]+)$#'];
        yield 'single placeholder without leading slash' => ['category/{id}', [], '#^/category/(?P<id>[^/]+)$#'];
        yield 'multiple placeholders' => [
            '/posts/{year}/{month}/{slug}',
            [],
            '#^/posts/(?P<year>[^/]+)/(?P<month>[^/]+)/(?P<slug>[^/]+)$#',
        ];
        yield 'adjacent placeholders and custom delimiters' => [
            '/category/{slug}-{id}',
            [],
            '#^/category/(?P<slug>[^/]+)-(?P<id>[^/]+)$#',
        ];
        yield 'custom constraints with slash delimiters as array' => [
            '/category/{slug}-{id}',
            [
                'slug' => '/[a-z0-9-]+/',
                'id' => '/^\d+$/',
            ],
            '#^/category/(?P<slug>[a-z0-9-]+)-(?P<id>\d+)$#',
        ];
        yield 'custom constraints with RouteArguments VO' => [
            '/category/{slug}-{id}',
            new RouteArguments(
                new RouteArgument('slug', '/[a-z0-9-]+/'),
                new RouteArgument('id', '/^\d+$/'),
            ),
            '#^/category/(?P<slug>[a-z0-9-]+)-(?P<id>\d+)$#',
        ];
        yield 'custom constraints with hash delimiters' => [
            '/items/{id}',
            new RouteArguments(new RouteArgument('id', '#^[0-9]+$#')),
            '#^/items/(?P<id>[0-9]+)$#',
        ];
        yield 'custom constraints with tilde delimiters' => [
            '/items/{code}',
            new RouteArguments(new RouteArgument('code', '~^[A-Z]+$~')),
            '#^/items/(?P<code>[A-Z]+)$#',
        ];
        yield 'unquantified rules wrapped with non-capturing group' => [
            '/category/{id}',
            new RouteArguments(new RouteArgument('id', '\d')),
            '#^/category/(?P<id>(?:\d)+)$#',
        ];
        yield 'quantified rules preserved' => [
            '/archive/{year}',
            new RouteArguments(new RouteArgument('year', '\d{4}')),
            '#^/archive/(?P<year>\d{4})$#',
        ];
        yield 'range quantified rules preserved' => [
            '/archive/{code}',
            new RouteArguments(new RouteArgument('code', '[a-z]{2,5}')),
            '#^/archive/(?P<code>[a-z]{2,5})$#',
        ];
    }
}
