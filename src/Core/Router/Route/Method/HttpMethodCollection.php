<?php

namespace App\Core\Router\Route\Method;

use Traversable;

class HttpMethodCollection implements \IteratorAggregate
{
    protected array $methods = [];

    /**
     * @param iterable<HttpMethodEnum> $methods
     */
    public function __construct(iterable $methods)
    {
        foreach ($methods as $method) {
            $this->methods[] = $method;
        }
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->methods);
    }

    public function has(HttpMethodEnum $method): bool
    {
        return in_array($method, $this->methods);
    }
}
