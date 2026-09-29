<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DummyTest extends TestCase
{
    #[Test]
    public function dummy()
    {
        self::assertTrue(true);
    }

}
