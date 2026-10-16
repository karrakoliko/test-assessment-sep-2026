<?php

namespace Tests\Unit\Core\Router\UrlGenerator;

use App\Core\Router\ControllerReferenceInterface;
use App\Core\Router\Route\Collection\RouteCollection;
use App\Core\Router\Route\Compiler\RouteCompilerInterface;
use App\Core\Router\Route\Route;
use App\Core\Router\UrlGenerator\UrlGenerator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Uri\Rfc3986\Uri;

class UrlGeneratorTest extends TestCase
{
    #[Test]
    public function generateThrowsExceptionWhenRouteNotFound(): void
    {
        $routes = new RouteCollection();
        $generator = new UrlGenerator($routes);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Route "non_existing_route" not found.');

        $generator->generate('non_existing_route');
    }

    #[Test]
    public function generateReturnsUriForStaticRoute(): void
    {
        $route = new Route(
            mask: '/home',
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            name: 'home'
        );
        $routes = new RouteCollection([$route]);
        $generator = new UrlGenerator($routes);

        $uri = $generator->generate('home');

        self::assertInstanceOf(Uri::class, $uri);
        self::assertSame('/home', $uri->toString());
    }

    #[Test]
    public function generateReturnsUriForParameterizedRoute(): void
    {
        $route = new Route(
            mask: '/category/{id}',
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            name: 'category_show'
        );
        $routes = new RouteCollection([$route]);
        $generator = new UrlGenerator($routes);

        $uri = $generator->generate('category_show', ['id' => 42]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertSame('/category/42', $uri->toString());
    }


    #[Test]
    public function generateUsesCustomCompilerWhenInjected(): void
    {
        $route = new Route(
            mask: '/custom/{name}',
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            name: 'custom_route'
        );
        $routes = new RouteCollection([$route]);

        $compilerMock = $this->createMock(RouteCompilerInterface::class);
        $compilerMock->expects(self::once())
            ->method('compileToUrl')
            ->with('/custom/{name}', ['name' => 'test'])
            ->willReturn(new Uri('/custom/test'));

        $generator = new UrlGenerator($routes, $compilerMock);

        $uri = $generator->generate('custom_route', ['name' => 'test']);

        self::assertSame('/custom/test', $uri->toString());
    }

    #[Test]
    public function generateThrowsExceptionWhenRequiredArgumentMissing(): void
    {
        $route = new Route(
            mask: '/category/{id}',
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            name: 'category_show'
        );
        $routes = new RouteCollection([$route]);
        $generator = new UrlGenerator($routes);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Missing required argument "id" for route mask "/category/{id}".');

        $generator->generate('category_show', []);
    }

    #[Test]
    public function generateReturnsUriWithQueryParams(): void
    {
        $route = new Route(
            mask: '/category/{id}',
            controllerReference: $this->createStub(ControllerReferenceInterface::class),
            name: 'category_show'
        );
        $routes = new RouteCollection([$route]);
        $generator = new UrlGenerator($routes);

        $uri = $generator->generate('category_show', ['id' => 42], ['sort' => 'desc', 'page' => 2]);

        self::assertInstanceOf(Uri::class, $uri);
        self::assertSame('/category/42?sort=desc&page=2', $uri->toString());
    }
}
