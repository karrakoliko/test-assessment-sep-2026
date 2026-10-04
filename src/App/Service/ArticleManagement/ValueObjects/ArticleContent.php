<?php

namespace App\App\Service\ArticleManagement\ValueObjects;

class ArticleContent implements \Stringable
{
    public const int MAX_LENGTH = 4_294_967_295; // LONGTEXT
    protected string $content;

    public function __construct(string $content)
    {
        $content = trim($content);

        if (strlen($content) > self::MAX_LENGTH) {
            throw new \InvalidArgumentException('Article content is too long');
        }

        $this->content = $content;
    }

    public function getValue(): string
    {
        return $this->content;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
