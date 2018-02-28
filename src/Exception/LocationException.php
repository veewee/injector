<?php

declare(strict_types=1);

namespace Injector\Exception;

class LocationException extends RuntimeException
{
    public static function fromToken(string $locationToken): self
    {
        return new self('Could not parse location token '.$locationToken);
    }

    public static function fromFullLocation(string $location): self
    {
        return new self('Could not parse location '.$location);
    }
}
