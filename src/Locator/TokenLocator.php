<?php

declare(strict_types=1);

namespace Injector\Locator;

use Injector\Exception\LocationException;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class TokenLocator
{
    /**
     * @var TokenOperatorInterface
     */
    private $tokenOperator;

    public function __construct(TokenOperatorInterface $tokenOperator)
    {
        $this->tokenOperator = $tokenOperator;
    }

    public function locate(Tokens $tokens, string $location): int
    {
        $index = 0;
        foreach ($this->parseLocationString($location) as $currentLocation) {
            $index = $this->tokenOperator->searchIndex($tokens, $index, $currentLocation);
            if (null === $index) {
                throw LocationException::fromToken($currentLocation);
            }
        }

        if (0 === $index) {
            throw LocationException::fromFullLocation($location);
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
}
