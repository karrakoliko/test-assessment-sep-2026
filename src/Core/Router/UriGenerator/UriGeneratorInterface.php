<?php

namespace App\Core\Router\UriGenerator;

use Uri\Rfc3986\Uri;

interface UriGeneratorInterface
{
    public function generate(string $routeName, array $arguments = []): Uri;

}
