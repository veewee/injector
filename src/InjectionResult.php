<?php

declare(strict_types=1);

namespace Injector;

use Symfony\Component\Finder\SplFileInfo;
use Throwable;

final class InjectionResult
{
    /**
     * @var SplFileInfo
     */
    private $file;

    /**
     * @var ?\Throwable
     */
    private $exception;

    /**
     * @var ?string
     */
    private $diff;

    /**
     * @var ?string
     */
    private $newCode;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function withException(Throwable $exception)
    {
        $result = clone $this;
        $result->exception = $exception;

        return $result;
    }

    public function withDiff(string $diff)
    {
        $result = clone $this;
        $result->diff = $diff;

        return $result;
    }

    public function withNewCode(string $newCode)
    {
        $result = clone $this;
        $result->newCode = $newCode;

        return $result;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    public function getException(): ?Throwable
    {
        return $this->exception;
    }

    public function hasException(): bool
    {
        return null !== $this->exception;
    }

    public function getDiff(): ?string
    {
        return $this->diff;
    }

    public function hasDiff(): bool
    {
        return null !== $this->diff;
    }

    public function getNewCode(): ?string
    {
        return $this->newCode;
    }

    public function hasNewCode(): bool
    {
        return null !== $this->newCode;
    }

    public function isSuccessfull(): bool
    {
        return $this->hasNewCode() && !$this->hasException();
    }
}
