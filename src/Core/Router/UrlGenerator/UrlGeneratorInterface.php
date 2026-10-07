<?php

namespace App\Core\Router\UrlGenerator;

use Uri\Rfc3986\Uri;

interface UrlGeneratorInterface
{
    public function generate(string $routeName, array $arguments = []): Uri;

}
