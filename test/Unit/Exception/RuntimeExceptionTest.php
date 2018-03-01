<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Exception;

use Injector\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Exception\RuntimeException
 */
class RuntimeExceptionTest extends TestCase
{
    public function test_it_is_a_runtime_exception(): void
    {
        $exception = new RuntimeException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function test_it_is_throwable(): void
    {
        $exception = new RuntimeException();
        $this->expectExceptionObject($exception);

        throw $exception;
    }
}
