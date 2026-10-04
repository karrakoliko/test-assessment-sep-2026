<?php

namespace Tests\Unit\Core\Router\Route;

use App\Core\Router\Route\Argument\RouteArgument;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class RouteArgumentTest extends TestCase
{
    #[Test]
    public function createsWithDefaultPattern(): void
    {
        $argument = new RouteArgument('id');

        self::assertSame('id', $argument->getName());
        self::assertSame('id', $argument->name);
        self::assertSame('[^/]+', $argument->getPattern());
        self::assertSame('[^/]+', $argument->pattern);
    }

    #[Test]
    #[DataProvider('provideNormalizedPatternCases')]
    public function normalizesPatternCorrectly(string $pattern, string $expectedNormalized): void
    {
        $argument = new RouteArgument('id', $pattern);

        self::assertSame($expectedNormalized, $argument->getPattern());
        self::assertSame($expectedNormalized, $argument->pattern);
    }

    public static function provideNormalizedPatternCases(): iterable
    {
        yield 'plain pattern' => ['\d+', '\d+'];
        yield 'slashes trimmed' => ['/^\d+$/', '\d+'];
        yield 'hash delimiters trimmed' => ['#^\d+$#', '\d+'];
        yield 'tilde delimiters trimmed' => ['~^\d+$~', '\d+'];
        yield 'custom pattern with quantifier' => ['[a-z0-9_-]{3,16}', '[a-z0-9_-]{3,16}'];
    }

    #[Test]
    public function throwsExceptionOnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Route argument name cannot be empty.');

        new RouteArgument('');
    }
}
