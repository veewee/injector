<?php

declare(strict_types=1);

namespace Injector\Exception;

class InputStreamException extends RuntimeException
{
    public static function fromInvalidResource($resource): self
    {
        return new self(
            sprintf('Expected a resource stream for reading the commandline input. Got %s.', gettype($resource))
        );
    }

    public static function fromEmptyStdIn(): self
    {
        return new self('No input detected. Please pipe data to stdin.');
    }
}
