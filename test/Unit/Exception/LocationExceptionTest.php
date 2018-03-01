<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Exception;

use Injector\Exception\LocationException;
use Injector\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Exception\LocationException
 */
class LocationExceptionTest extends TestCase
{
    public function test_it_is_a_runtime_exception(): void
    {
        $exception = new LocationException();
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function test_it_is_throwable(): void
    {
        $exception = new LocationException();
        $this->expectExceptionObject($exception);

        throw $exception;
    }

    public function test_it_can_throw_from_token(): void
    {
        $exception = LocationException::fromToken('token');
        $this->expectExceptionObject($exception);

        throw $exception;
    }

    public function test_it_can_throw_from_full_token_string(): void
    {
        $exception = LocationException::fromFullLocation('token');
        $this->expectExceptionObject($exception);

        throw $exception;
    }
}
