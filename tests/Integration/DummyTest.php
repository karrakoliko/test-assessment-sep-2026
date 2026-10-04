<?php

namespace Tests\Integration;

use PHPUnit\Framework\Attributes\Test;

class DummyTest extends TestCaseIntegration
{
    #[Test]
    public function dummy(
    ) {
        self::assertTrue(true);
    }

}
