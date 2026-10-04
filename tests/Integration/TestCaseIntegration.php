<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

class TestCaseIntegration extends TestCase
{
    protected static Container $container;

    public function setUp(): void
    {
        parent::setUp();

        self::$container = (require __DIR__ . '/../../config/services.php')();
    }

}
