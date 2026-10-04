<?php

namespace Tests\Unit\Core\Router\ArgumentResolver;

use App\Core\Router\ArgumentResolver\ArgumentResolver;
use App\Core\Router\ControllerReferenceInterface;
use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Route;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ArgumentResolverTest extends TestCase
{
    #[Test]
    #[DataProvider('provideResolutionCases')]
    public function resolveArguments(string $mask, RouteArguments|array $arguments, string $url, array $expected): void
    {
        $resolver = new ArgumentResolver();
        $route = new Route(
            mask: $mask,
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            arguments: $arguments
        );

        $args = $resolver->resolveArguments(
            request: new Request('GET', $url),
            route: $route
        );

        self::assertSame($expected, $args);
    }

    public static function provideResolutionCases(): iterable
    {
        yield 'extracts arguments with constraints as array' => [
            '/category/{slug}-{id}',
            [
                'slug' => '/([a-z\d\-])/',
                'id' => '/^(\d+)$/',
            ],
            'https://example.com/category/who-cares-12',
            ['slug' => 'who-cares', 'id' => '12'],
        ];

        yield 'extracts arguments with RouteArguments VO constraints' => [
            '/category/{slug}-{id}',
            new RouteArguments(
                new RouteArgument('slug', '/([a-z\d\-])/'),
                new RouteArgument('id', '/^(\d+)$/'),
            ),
            'https://example.com/category/who-cares-12',
            ['slug' => 'who-cares', 'id' => '12'],
        ];

        yield 'extracts arguments without constraints' => [
            '/posts/{year}/{month}/{slug}',
            [],
            'https://example.com/posts/2026/10/hello-world',
            [
                'year' => '2026',
                'month' => '10',
                'slug' => 'hello-world',
            ],
        ];

        yield 'returns empty array when route has no placeholders' => [
            '/',
            [],
            'https://example.com/',
            [],
        ];

        yield 'returns empty array on route mismatch' => [
            '/category/{id}',
            [],
            'https://example.com/other/path',
            [],
        ];
    }
}
