<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\PreviousNonWhitespaceTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\PreviousNonWhitespaceTokenOperator
 */
class PreviousNonWhitespaceTokenOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new PreviousNonWhitespaceTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new PreviousNonWhitespaceTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new PreviousNonWhitespaceTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['<<', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '<<', -2, null],
            ['<?php class SomeClass { }', '<<', 1, 0],
            ['<?php class SomeClass { }', '<<', 3, 1],
            ['<?php class SomeClass { }', '<<', 5, 3],
            ['<?php class SomeClass { }', '<<', 7, 5],
            ['<?php class SomeClass { }', '<<', 20, null],
            ['<?php /** @docs **/class SomeClass { }', '>>', 1, 0],
            ['<?php /** @docs **/class SomeClass { }', '>>', 4, 2],
            ['<?php /** @docs **/class SomeClass { }', '>>', 2, 1],
        ];
    }
}
