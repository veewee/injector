<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\NextMeaningfulTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\NextMeaningfulTokenOperator
 */
class NextMeaningfulTokenOperatorTest extends TestCase
{
    function test_it_is_a_token_operator()
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new NextMeaningfulTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    function test_it_operates(string $location, bool $expected)
    {
        $operator = new NextMeaningfulTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected)
    {
        $operator = new NextMeaningfulTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['>>>', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '>>>', -2, null],
            ['<?php class SomeClass { }', '>>>', 0, 1],
            ['<?php class SomeClass { }', '>>>', 2, 3],
            ['<?php class SomeClass { }', '>>>', 4, 5],
            ['<?php class SomeClass { }', '>>>', 7, null],
            ['<?php class SomeClass { }', '>>>', 20, null],
            ['<?php /** @docs **/class SomeClass { }', '>>>', 0, 2],
            ['<?php /** @docs **/ /*Some comment*/ class SomeClass { }', '>>>', 0, 5],
        ];
    }
}
