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
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new BracesOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new BracesOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
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
