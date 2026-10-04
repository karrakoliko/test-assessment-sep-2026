<?php

namespace App\App\Service\CategoryManagement\ValueObjects;

class CategoryTitle implements \Stringable
{
    public const int MAX_LENGTH = 255; // VARCHAR(255)

    public function __construct(protected string $title)
    {
        if (strlen($title) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('Category title is too long');
        }
    }

    public function getValue(): string
    {
        return $this->title;
    }

    public function __toString(
    ): string {
        return $this->getValue();
    }
}
