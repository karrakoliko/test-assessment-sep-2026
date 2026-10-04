<?php

namespace App\App\Service\CategoryManagement\ValueObjects;

class CategoryTitle implements \Stringable
{
    public const int MAX_LENGTH = 255; // VARCHAR(255)
    protected string $title;

    public function __construct(string $title)
    {
        $title = trim($title);

        if (strlen($title) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('Category title is too long');
        }

        $this->title = $title;
    }

    public function getValue(): string
    {
        return $this->title;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
