<?php

namespace App\Core\Router\Route\Argument;

/**
 * @implements \IteratorAggregate<string, RouteArgument>
 */
final readonly class RouteArguments implements \IteratorAggregate, \Countable
{
    /** @var array<string, RouteArgument> */
    private array $arguments;

    public function __construct(RouteArgument ...$arguments)
    {
        $map = [];
        foreach ($arguments as $argument) {
            $map[$argument->name] = $argument;
        }
        $this->arguments = $map;
    }

    /**
     * @param array<string|int, string|int|float|\Stringable|RouteArgument> $arguments
     */
    public static function fromArray(array $arguments): self
    {
        $items = [];
        foreach ($arguments as $name => $arg) {
            if ($arg instanceof RouteArgument) {
                $items[] = $arg;
            } elseif (is_string($name) && (is_scalar($arg) || $arg instanceof \Stringable)) {
                $items[] = new RouteArgument($name, (string) $arg);
            }
        }
        return new self(...$items);
    }

    public function has(string $name): bool
    {
        return isset($this->arguments[$name]);
    }

    public function get(string $name): ?RouteArgument
    {
        return $this->arguments[$name] ?? null;
    }


    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return array_map(fn (RouteArgument $arg) => $arg->pattern, $this->arguments);
    }

    public function count(): int
    {
        return count($this->arguments);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator((array)$this->arguments);
    }
}
