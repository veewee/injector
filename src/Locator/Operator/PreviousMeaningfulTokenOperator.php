<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

final class PreviousMeaningfulTokenOperator implements TokenOperatorInterface
{
    public function operates(string $location): bool
    {
        return '<<<' === $location;
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->getPrevMeaningfulToken($previousIndex);
    }
}
