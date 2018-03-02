<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\PreviousTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\PreviousTokenOperator
 */
class PreviousTokenOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new PreviousTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new PreviousTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new PreviousTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['<', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '<', -1, null],
            ['<?php class SomeClass { }', '<', 0, null],
            ['<?php class SomeClass { }', '<', 1, 0],
            ['<?php class SomeClass { }', '<', 2, 1],
            ['<?php class SomeClass { }', '<', 8, 7],
            ['<?php class SomeClass { }', '<', 9, null],
            ['<?php class SomeClass { }', '<', 10, null],
            ['<?php class SomeClass { }', '<', 99, null],
            ['<?php class SomeClass { }', '<', 0, null],
        ];
    }
}
