<?php

declare(strict_types=1);

namespace Injector\Locator\Operator;

use Injector\Exception\LocationException;
use PhpCsFixer\Tokenizer\Tokens;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
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

        array_map(
            function (ReflectionClass $class) use ($stack): void {
                $stack->add($class->newInstance());
            },
            array_filter(
                array_map(
                    function (SplFileInfo $file) {
                        return new ReflectionClass(__NAMESPACE__.'\\'.$file->getBasename('.php'));
                    },
                    iterator_to_array($files)
                ),
                function (ReflectionClass $class) {
                    return __CLASS__ !== $class->getName() && !$class->isInterface();
                }
            )
        );

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
