<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\PreviousMeaningfulTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\PreviousMeaningfulTokenOperator
 */
class PreviousMeaningfulTokenOperatorTest extends TestCase
{
    function test_it_is_a_token_operator()
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new PreviousMeaningfulTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    function test_it_operates(string $location, bool $expected)
    {
        $operator = new PreviousMeaningfulTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected)
    {
        $operator = new PreviousMeaningfulTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['<<<', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '<<<', -2, null],
            ['<?php class SomeClass { }', '<<<', 1, 0],
            ['<?php class SomeClass { }', '<<<', 3, 1],
            ['<?php class SomeClass { }', '<<<', 5, 3],
            ['<?php class SomeClass { }', '<<<', 7, 5],
            ['<?php class SomeClass { }', '<<<', 20, null],
            ['<?php /** @docs **/class SomeClass { }', '<<<', 1, 0],
            ['<?php /** @docs **/class SomeClass { }', '<<<', 4, 2],
            ['<?php /** @docs **/class SomeClass { }', '<<<', 2, 0],
            ['<?php /** @docs **/ /*Some comment*/ class SomeClass { }', '<<<', 5, 0],
        ];
    }
}
