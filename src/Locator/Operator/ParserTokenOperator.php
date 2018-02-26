<?php

declare(strict_types=1);

namespace CopyPaste\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

class ParserTokenOperator implements TokenOperatorInterface
{
    public function operates(string $location): bool
    {
        return strpos($location, 'T_') === 0 && \defined($location);
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->getNextTokenOfKind($previousIndex, [[\constant($location)]]);
    }
}
