<?php

namespace App\Core\Router\Route\Argument;

final readonly class RouteArgument
{
    public string $pattern;

    public function __construct(
        public string $name,
        string $pattern = '[^/]+'
    ) {
        if ($this->name === '') {
            throw new \InvalidArgumentException('Route argument name cannot be empty.');
        }
        $this->pattern = trim($pattern, "/^$#~");
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }
}
