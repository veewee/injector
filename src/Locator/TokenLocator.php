<?php

declare(strict_types=1);

namespace CopyPaste\Locator;

use PhpCsFixer\Tokenizer\Tokens;

class TokenLocator
{
    /**
     * @var Operator\TokenOperatorInterface[]
     */
    private $operators;

    public function __construct()
    {
        $this->operators = [
            new Operator\BracesOperator(),
            new Operator\CtTokenOperator(),
            new Operator\MethodByNameOperator(),
            new Operator\NextMeaningfulTokenOperator(),
            new Operator\NextNonWhitespaceTokenOperator(),
            new Operator\NextTokenOperator(),
            new Operator\ParserTokenOperator(),
            new Operator\PreviousMeaningfulTokenOperator(),
            new Operator\PreviousNonWhitespaceTokenOperator(),
            new Operator\PreviousTokenOperator(),
        ];
    }

    public function locate(Tokens $tokens, string $location): int
    {
        $index = 0;
        foreach ($this->parseLocationString($location) as $currentLocation) {
            $index = $this->searchIndex($tokens, $index, $currentLocation);
        }

        if (0 === $index) {
            throw new \RuntimeException('Could not locate ' . $location);
        }

        return $index;
    }

    private function parseLocationString(string $location): array
    {
        return array_filter(
            array_map(
                'trim',
                explode(' ', $location)
            )
        );
    }

    private function searchIndex(Tokens $tokens, int $previousIndex, string $location): int
    {
        $operator = $this->getOperatorForLocation($location);
        $index = $operator->searchIndex($tokens, $previousIndex, $location);

        if (null === $index) {
            throw new \RuntimeException('The token ' . $location . ' could not be found.');
        }

        return $index;
    }

    private function getOperatorForLocation(string $location): Operator\TokenOperatorInterface
    {
        foreach ($this->operators as $operator) {
            if ($operator->operates($location)) {
                return $operator;
            }
        }

        throw new \RuntimeException('Could not detect operator for location ' . $location);
    }
}
