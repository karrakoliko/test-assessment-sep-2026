<?php

namespace Tests\Unit\Core\Router\Route;

use App\Core\Router\Route\Argument\RouteArgument;
use App\Core\Router\Route\Argument\RouteArguments;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteArgumentsTest extends TestCase
{
    #[Test]
    public function createsEmptyCollection(): void
    {
        $arguments = new RouteArguments();

        self::assertCount(0, $arguments);
        self::assertFalse($arguments->has('id'));
        self::assertNull($arguments->get('id'));
        self::assertSame([], $arguments->toArray());
    }

    #[Test]
    public function createsWithVariadicArguments(): void
    {
        $arg1 = new RouteArgument('id', '/^\d+$/');
        $arg2 = new RouteArgument('slug', '[a-z-]+');

        $arguments = new RouteArguments($arg1, $arg2);

        self::assertCount(2, $arguments);
        self::assertTrue($arguments->has('id'));
        self::assertTrue($arguments->has('slug'));
        self::assertFalse($arguments->has('unknown'));

        self::assertSame($arg1, $arguments->get('id'));
        self::assertSame($arg2, $arguments->get('slug'));

        self::assertSame([
            'id' => '\d+',
            'slug' => '[a-z-]+',
        ], $arguments->toArray());
    }

    #[Test]
    public function createsFromArrayWithStringsAndInstances(): void
    {
        $arguments = RouteArguments::fromArray([
            'id' => '/^\d+$/',
            'slug' => new RouteArgument('slug', '[a-z-]+'),
        ]);

        self::assertCount(2, $arguments);
        self::assertTrue($arguments->has('id'));
        self::assertTrue($arguments->has('slug'));
        self::assertSame('\d+', $arguments->get('id')?->getPattern());
        self::assertSame('[a-z-]+', $arguments->get('slug')?->getPattern());
    }

    #[Test]
    public function iteratesOverRouteArguments(): void
    {
        $arg1 = new RouteArgument('id', '\d+');
        $arg2 = new RouteArgument('slug', '[a-z-]+');

        $arguments = new RouteArguments($arg1, $arg2);

        $iterated = [];
        foreach ($arguments as $name => $arg) {
            $iterated[$name] = $arg;
        }

        self::assertSame([
            'id' => $arg1,
            'slug' => $arg2,
        ], $iterated);
    }
}
