<?php

namespace Tests\Unit\Core\Router;

use App\Core\Router\ControllerReference;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\TestCaseUnit;

class ControllerReferenceTest extends TestCaseUnit
{
    #[Test]
    public function createsInstanceWhenClassAndMethodExist(): void
    {
        $reference = new ControllerReference(
            class: self::class,
            method: 'createsInstanceWhenClassAndMethodExist'
        );

        self::assertSame(self::class, $reference->getClass());
        self::assertSame('createsInstanceWhenClassAndMethodExist', $reference->getMethod());
    }

    #[Test]
    public function throwsExceptionWhenClassDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs('Controller class "NonExistentControllerClass" does not exist.');

        new ControllerReference(
            class: 'NonExistentControllerClass',
            method: 'index'
        );
    }

    #[Test]
    public function throwsExceptionWhenMethodDoesNotExist(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageIs(sprintf('Controller method "%s::nonExistentMethod" does not exist.', self::class));

        new ControllerReference(
            class: self::class,
            method: 'nonExistentMethod'
        );
    }
}
