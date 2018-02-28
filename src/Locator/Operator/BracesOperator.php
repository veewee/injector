<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

final class BracesOperator implements TokenOperatorInterface
{
    private const LOOKUPS = [
        '{' => ['{'],
        '}' => ['}'],
        '(' => ['(', [CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN]],
        ')' => [')', [CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]],
        '[' => ['[', [CT::T_ARRAY_SQUARE_BRACE_OPEN]],
        ']' => ['[', [CT::T_ARRAY_SQUARE_BRACE_CLOSE]],
    ];

    public function operates(string $location): bool
    {
        return array_key_exists($location, self::LOOKUPS);
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        $lookupTokens = self::LOOKUPS[$location];
        return $tokens->getNextTokenOfKind($previousIndex, $lookupTokens);
    }
}
