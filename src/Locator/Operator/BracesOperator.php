<?php

declare(strict_types=1);

namespace CopyPaste\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

class BracesOperator implements TokenOperatorInterface
{
    private const SUPPORTED_CHARS = '{}()[]';

    public function operates(string $location): bool
    {
        return strpos(self::SUPPORTED_CHARS, $location) !== false;
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->getNextTokenOfKind($previousIndex, [$location]);
    }
}
