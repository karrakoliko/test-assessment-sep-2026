<?php

namespace Tests\Unit\Core\Router\Route;

use App\Core\Router\ControllerReferenceInterface;
use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use App\Core\Router\Route\Route;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    #[Test]
    #[DataProvider('provideMatchingCases')]
    public function matchesRequest(string $mask, RouteArguments|array $arguments, string $path, bool $expected): void
    {
        $route = new Route(
            mask: $mask,
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            arguments: $arguments
        );

        self::assertSame($expected, $route->matches(new ServerRequest('GET', $path)));
        self::assertInstanceOf(RouteArguments::class, $route->getArguments());
    }

    public static function provideMatchingCases(): iterable
    {
        yield 'regex route match' => ['/^\/a\/b\/[0-9]$/', [], '/a/b/1', true];
        yield 'root path match' => ['/', [], '/', true];
        yield 'root path mismatch' => ['/', [], '/category/1', false];
        yield 'single placeholder match leading slash' => ['category/{id}', [], '/category/1', true];
        yield 'single placeholder match without leading slash' => ['category/{id}', [], 'category/1', true];
        yield 'single placeholder mismatch' => ['category/{id}', [], '/category/1/show', false];
        yield 'nested placeholder match' => ['category/{id}/show', [], '/category/1/show', true];
        yield 'nested placeholder mismatch' => ['category/{id}/show', [], '/category/1/edit', false];
        yield 'constraint match with array' => ['/category/{id}', ['id' => '/^\d+$/'], '/category/123', true];
        yield 'constraint match with RouteArguments VO' => [
            '/category/{id}',
            new RouteArguments(new RouteArgument('id', '/^\d+$/')),
            '/category/123',
            true,
        ];
        yield 'constraint mismatch' => [
            '/category/{id}',
            new RouteArguments(new RouteArgument('id', '/^\d+$/')),
            '/category/abc',
            false,
        ];
    }
}
