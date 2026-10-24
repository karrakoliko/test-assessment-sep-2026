<?php

namespace Tests\Unit\App\Service\ArticleManagement\ValueObjects;

use App\App\Service\ArticleManagement\ValueObjects\ArticlePicUri;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;
use Uri\Rfc3986\Uri;

class ArticlePicUriTest extends TestCaseUnit
{
    #[Test]
    public function throwsIfLongerThanDbLimit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Article pic url is too long');

        new ArticlePicUri(str_repeat('a', ArticlePicUri::MAX_LENGTH + 1));
    }

    #[Test]
    public function throwsIfUriSchemeIsNotHttpOrHttps(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Article pic url scheme must be http or https');

        new ArticlePicUri('ftp://some/resource');
    }

    #[Test]
    public function throwsIfInvalidUri(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Article pic url is invalid');

        new ArticlePicUri('http://[::1');
    }

    #[Test]
    public function asString(): void
    {
        $picUrl = new ArticlePicUri('https://example.com/image.png');

        self::assertSame('https://example.com/image.png', $picUrl->toString());
        self::assertSame('https://example.com/image.png', (string) $picUrl);
    }

    #[Test]
    public function asUriObject(): void
    {
        $picUrl = new ArticlePicUri('https://example.com/image.png');

        self::assertInstanceOf(Uri::class, $picUrl->toUri());
        self::assertSame((new Uri('https://example.com/image.png'))->toRawString(), $picUrl->toUri()->toRawString());
    }
}
