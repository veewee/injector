<?php

declare(strict_types=1);

namespace CopyPaste\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

class NextTokenOperator implements TokenOperatorInterface
{
    public function operates(string $location): bool
    {
        return '>' === $location;
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        $nextIndex = $previousIndex + 1;
        return ($nextIndex <= $tokens->getSize()) ? $nextIndex : null;
    }
}
