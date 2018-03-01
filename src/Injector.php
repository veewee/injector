<?php

declare(strict_types=1);

namespace Injector;

use Injector\Locator\TokenLocator;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Tokenizer\Tokens;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

final class Injector
{
    /**
     * @var TokenLocator
     */
    private $tokenLocator;

    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var LinterInterface
     */
    private $linter;

    public function __construct(TokenLocator $tokenLocator, Differ $differ, LinterInterface $linter)
    {
        $this->tokenLocator = $tokenLocator;
        $this->differ = $differ;
        $this->linter = $linter;
    }

    public function inject(SplFileInfo $file, Tokens $data, string $location): InjectionResult
    {
        $result = new InjectionResult($file);

        try {
            $sourceCode = $file->getContents();
            $tokens = Tokens::fromCode($sourceCode);
            $index = $this->tokenLocator->locate($tokens, $location);
            $tokens->insertAt($index, $data);

            $newCode = $tokens->generateCode();
            $result = $result->withNewCode($newCode);

            $this->linter->lintSource($newCode)->check();
        } catch (Throwable $exception) {
            $result = $result->withException($exception);
        }

        if (isset($sourceCode, $newCode)) {
            $result = $this->runDiff($result, $sourceCode, $newCode);
        }

        return $result;
    }

    private function runDiff(InjectionResult $result, string $sourceCode, string $newCode): InjectionResult
    {
        try {
            $result = $result->withDiff(
                $this->differ->diff($sourceCode, $newCode)
            );
        } catch (\Throwable $exception) {
            if (!$result->hasException()) {
                $result = $result->withException($exception);
            }
        }

        return $result;
    }
}
