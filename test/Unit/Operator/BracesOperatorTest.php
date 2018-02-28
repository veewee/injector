<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\BracesOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\BracesOperator
 */
class BracesOperatorTest extends TestCase
{
    function test_it_is_a_token_operator()
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new BracesOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    function test_it_operates(string $location, bool $expected)
    {
        $operator = new BracesOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected)
    {
        $operator = new BracesOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['{', true],
            ['}', true],
            ['[', true],
            [']', true],
            ['(', true],
            [')', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '{', 0, 5],
            ['<?php class SomeClass { }', '}', 0, 7],
            ['<?php [1];', '[', 0, 1],
            ['<?php [1];', ']', 0, 3],
            ['<?php array(1);', '(', 0, 2],
            ['<?php array(1);', ')', 0, 4],
            ['<?php ($data);', '(', 0, 1],
            ['<?php ($data);', ')', 0, 3],
            ['<?php', '{', 0, null],
            ['<?php', '}', 0, null],
            ['<?php', '[', 0, null],
            ['<?php', ']', 0, null],
            ['<?php', '(', 0, null],
            ['<?php', ')', 0, null],
        ];
    }
}
