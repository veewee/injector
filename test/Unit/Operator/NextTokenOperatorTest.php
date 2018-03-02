<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\NextTokenOperator;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\NextTokenOperator
 */
class NextTokenOperatorTest extends TestCase
{
    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new NextTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new NextTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['>', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { }', '>', -2, null],
            ['<?php class SomeClass { }', '>', -1, 0],
            ['<?php class SomeClass { }', '>', 0, 1],
            ['<?php class SomeClass { }', '>', 1, 2],
            ['<?php class SomeClass { }', '>', 7, 8],
            ['<?php class SomeClass { }', '>', 8, null],
            ['<?php class SomeClass { }', '>', 100, null],
        ];
    }
}
