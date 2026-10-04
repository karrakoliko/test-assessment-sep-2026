<?php

namespace App\Core\Router;

readonly class ControllerReference implements
    ControllerReferenceInterface
{
    public function __construct(
        public string $class,
        public string $method,
    ) {
        if (!class_exists($this->class)) {
            throw new \InvalidArgumentException(sprintf('Controller class "%s" does not exist.', $this->class));
        }

        if (!method_exists($this->class, $this->method)) {
            throw new \InvalidArgumentException(sprintf('Controller method "%s::%s" does not exist.', $this->class, $this->method));
        }
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
