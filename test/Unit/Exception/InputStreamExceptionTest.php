<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Exception;

use Injector\Exception\InputStreamException;
use Injector\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Exception\InputStreamException
 */
class InputStreamExceptionTest extends TestCase
{
    public function test_it_is_a_runtime_exception(): void
    {
        $exception = new InputStreamException();
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function test_it_is_throwable(): void
    {
        $exception = new InputStreamException();
        $this->expectExceptionObject($exception);

        throw $exception;
    }

    public function test_it_can_throw_from_invalid_resource(): void
    {
        $exception = InputStreamException::fromInvalidResource(new \stdClass());
        $this->expectExceptionObject($exception);

        throw $exception;
    }

    public function test_it_can_throw_from_empty_body(): void
    {
        $exception = InputStreamException::fromEmptyInputStream();
        $this->expectExceptionObject($exception);

        throw $exception;
    }
}
