<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\CtTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\CtTokenOperator
 */
class CtTokenOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new CtTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new CtTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new CtTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        $rc = new \ReflectionClass(CT::class);
        $constants = $rc->getConstants();

        return array_merge(
            [
                ['INVALID', false],
            ],
            array_map(
                function ($constantName) {
                    return [$constantName, true];
                },
                array_keys($constants)
            )
        );
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php [1];', 'T_ARRAY_SQUARE_BRACE_OPEN', 0, 1],
            ['<?php [1];', 'T_ARRAY_SQUARE_BRACE_CLOSE', 0, 3],
            ['<?php ', 'T_ARRAY_SQUARE_BRACE_CLOSE', 0, null],
        ];
    }
}
