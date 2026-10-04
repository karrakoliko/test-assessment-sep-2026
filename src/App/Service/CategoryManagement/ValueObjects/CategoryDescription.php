<?php

namespace App\App\Service\CategoryManagement\ValueObjects;

class CategoryDescription implements \Stringable
{
    public const int MAX_LENGTH = 4_294_967_295; // LONGTEXT

    public function __construct(protected string $description)
    {
        if (strlen($description) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('Category description is too long');
        }
    }

    public function getValue(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
