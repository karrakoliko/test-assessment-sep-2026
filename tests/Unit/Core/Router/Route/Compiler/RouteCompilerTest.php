<?php

namespace Tests\Unit\Core\Router\Route\Compiler;

use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Compiler\RouteCompiler;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Uri\Rfc3986\Uri;

class RouteCompilerTest extends TestCase
{
    #[Test]
    #[DataProvider('provideRegexCompilationCases')]
    public function compileToRegex(string $mask, RouteArguments|array $arguments, string $expectedRegex): void
    {
        $compiler = new RouteCompiler();

        self::assertSame($expectedRegex, $compiler->compileToRegex($mask, $arguments));
    }

    #[Test]
    #[DataProvider('provideUrlCompilationCases')]
    public function compileToUrl(string $mask, array $arguments, string $expectedUrl): void
    {
        $compiler = new RouteCompiler();

        $uri = $compiler->compileToUrl($mask, $arguments);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertSame($expectedUrl, $uri->toString());
    }

    #[Test]
    public function compileToUrlThrowsExceptionWhenArgumentIsMissing(): void
    {
        $compiler = new RouteCompiler();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Missing required argument "id" for route mask "/category/{id}".');

        $compiler->compileToUrl('/category/{id}', []);
    }

    public static function provideUrlCompilationCases(): iterable
    {
        yield 'root slash' => ['/', [], '/'];
        yield 'empty string' => ['', [], '/'];
        yield 'static path with trailing slash' => ['posts/all/', [], '/posts/all'];
        yield 'static path' => ['/posts/all', [], '/posts/all'];
        yield 'single placeholder with leading slash' => ['/category/{id}', ['id' => 123], '/category/123'];
        yield 'single placeholder without leading slash' => ['category/{id}', ['id' => 'abc'], '/category/abc'];
        yield 'multiple placeholders' => [
            '/posts/{year}/{month}/{slug}',
            ['year' => 2026, 'month' => 10, 'slug' => 'hello-world'],
            '/posts/2026/10/hello-world',
        ];
        yield 'adjacent placeholders and custom delimiters' => [
            '/category/{slug}-{id}',
            ['slug' => 'tech', 'id' => 42],
            '/category/tech-42',
        ];
    }

    public static function provideRegexCompilationCases(): iterable
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
            '#^/category/(?P<slug>[^/]+)\-(?P<id>[^/]+)$#',
        ];
        yield 'custom constraints with slash delimiters as array' => [
            '/category/{slug}-{id}',
            [
                'slug' => '/[a-z0-9-]+/',
                'id' => '/^\d+$/',
            ],
            '#^/category/(?P<slug>[a-z0-9-]+)\-(?P<id>\d+)$#',
        ];
        yield 'custom constraints with RouteArguments VO' => [
            '/category/{slug}-{id}',
            new RouteArguments(
                new RouteArgument('slug', '/[a-z0-9-]+/'),
                new RouteArgument('id', '/^\d+$/'),
            ),
            '#^/category/(?P<slug>[a-z0-9-]+)\-(?P<id>\d+)$#',
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
        yield 'static dot is treated literally' => [
            '/file/{name}.json',
            [],
            '#^/file/(?P<name>[^/]+)\.json$#',
        ];

        yield 'regex metacharacters in static path are escaped' => [
            '/v1.0/files/{id}+',
            [],
            '#^/v1\.0/files/(?P<id>[^/]+)\+$#',
        ];
    }
}
