<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

final class ParserTokenOperator implements TokenOperatorInterface
{
    public function operates(string $location): bool
    {
        return 0 === mb_strpos($location, 'T_') && \defined($location);
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->getNextTokenOfKind($previousIndex, [[\constant($location)]]);
    }
}
