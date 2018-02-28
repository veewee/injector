<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use PhpCsFixer\Tokenizer\Tokens;

interface TokenOperatorInterface
{
    public function operates(string $location): bool;

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int;
}
