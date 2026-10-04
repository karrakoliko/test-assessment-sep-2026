<?php

namespace App\App\Service\ArticleManagement\ValueObjects;

class ArticleDescription implements \Stringable
{
    public const int MAX_LENGTH = 4_294_967_295; // LONGTEXT
    protected string $description;

    public function __construct(string $description)
    {
        $description = trim($description);

        if (strlen($description) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('Article description is too long');
        }

        $this->description = $description;
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
