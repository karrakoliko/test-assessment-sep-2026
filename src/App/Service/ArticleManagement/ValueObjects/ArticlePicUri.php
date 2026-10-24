<?php

namespace App\App\Service\ArticleManagement\ValueObjects;

use Uri\InvalidUriException;
use Uri\Rfc3986\Uri;

class ArticlePicUri implements
    \Stringable
{
    public const int MAX_LENGTH = 65_535; // TEXT
    private Uri $value;

    public function __construct(Uri|string $picUri)
    {

        try {
            $uri = ($picUri instanceof Uri) ? $picUri : new Uri(trim($picUri));

            if (strlen($uri->toString()) > self::MAX_LENGTH) {
                throw new \InvalidArgumentException(
                    'Article pic url is too long'
                );
            }

            if (!in_array($uri->getScheme(), ['http','https'])) {
                throw new \InvalidArgumentException('Article pic url scheme must be http or https');
            }
            $this->value = $uri;
        } catch (InvalidUriException $e) {
            throw new \InvalidArgumentException(
                message: 'Article pic url is invalid',
                previous: $e
            );
        }
    }

    public function toUri(): Uri
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->toString();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
