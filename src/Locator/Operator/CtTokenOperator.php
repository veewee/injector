<?php

declare(strict_types=1);

namespace CopyPaste\Locator\Operator;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

class CtTokenOperator implements TokenOperatorInterface
{
    public function operates(string $location): bool
    {
        return \defined($this->parseCtTokenString($location));
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->getNextTokenOfKind($previousIndex, [[\constant($this->parseCtTokenString($location))]]);
    }

    private function parseCtTokenString(string $location): string
    {
        return CT::class . '::' . $location;
    }
}
