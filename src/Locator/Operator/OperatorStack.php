<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use Injector\Exception\LocationException;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Finder\Finder;
use Throwable;

final class OperatorStack implements TokenOperatorInterface
{
    /**
     * @var TokenOperatorInterface[]
     */
    private $operators;

    public function __construct(TokenOperatorInterface ...$operators)
    {
        $this->operators = $operators;
    }

    public static function getDefaultStack(): self
    {
        $stack = new self();
        $files = Finder::create()
            ->files()
            ->in(__DIR__)
            ->getIterator();

        foreach ($files as $file) {
            $class = new \ReflectionClass(__NAMESPACE__.'\\'.$file->getBasename('.php'));
            // Skip current class and interfaces
            if (__CLASS__ === $class->getName() || $class->isInterface()) {
                continue;
            }

            $stack->add($class->newInstance());
        }

        return $stack;
    }

    public function add(TokenOperatorInterface $operator): void
    {
        $this->operators[] = $operator;
    }

    public function operates(string $location): bool
    {
        try {
            $this->getOperatorForLocation($location);
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    public function searchIndex(Tokens $tokens, int $previousIndex, string $location): ?int
    {
        $operator = $this->getOperatorForLocation($location);

        return $operator->searchIndex($tokens, $previousIndex, $location);
    }

    private function getOperatorForLocation(string $location): TokenOperatorInterface
    {
        foreach ($this->operators as $operator) {
            if ($operator->operates($location)) {
                return $operator;
            }
        }

        throw LocationException::fromToken($location);
    }
}
