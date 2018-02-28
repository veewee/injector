<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use Injector\Exception\LocationException;
use PhpCsFixer\Tokenizer\Tokens;

final class StartBlockOperator implements TokenOperatorInterface
{
    private const START_BLOCK_REGEX = '/STARTBLOCK\(([\[\{\(])\)/';

    private static $supportedOperators = [
        '[' => Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE,
        '{' => Tokens::BLOCK_TYPE_CURLY_BRACE,
        '(' => Tokens::BLOCK_TYPE_PARENTHESIS_BRACE,
    ];

    public function operates(string $location): bool
    {
        return (bool) preg_match(self::START_BLOCK_REGEX, $location);
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        return $tokens->findBlockStart($this->parseBraceType($location), $previousIndex);
    }

    private function parseBraceType($location): int
    {
        $matches = [];
        preg_match(self::START_BLOCK_REGEX, $location, $matches);
        $brace = $matches[1] ?? '';

        if (!array_key_exists($brace, self::$supportedOperators)) {
            throw new LocationException('Could not detect endblock brace for '.$brace);
        }

        return self::$supportedOperators[$brace];
    }
}
