<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\BracesOperator;
use Injector\Locator\Operator\StartBlockOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\StartBlockOperator
 */
class StartBlockOperatorTest extends TestCase
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
        $operator = new StartBlockOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new StartBlockOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    /**
     * @dataProvider providesExceptionSearchIndexesData
     */
    public function test_it_throws_exceptions_on_invalid_data(string $code, string $location, int $previousIndex): void
    {
        $this->expectException(\Throwable::class);

        $operator = new StartBlockOperator();
        $tokens = Tokens::fromCode($code);
        $operator->searchIndex($tokens, $previousIndex, $location);
    }

    public function providesOperatesData()
    {
        return [
            ['STARTBLOCK({)', true],
            ['STARTBLOCK([)', true],
            ['STARTBLOCK(()', true],
            ['startblock({)', false],
            ['STARTBLOCK(INVALID)', false],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { function x() { return []; } }', 'STARTBLOCK({)', 23, 5],
            ['<?php class SomeClass { function x() { return []; } }', 'STARTBLOCK(()', 11, 10],
            ['<?php class SomeClass { function x() { return []; } }', 'STARTBLOCK({)', 21, 13],
            ['<?php class SomeClass { function x() { return []; } }', 'STARTBLOCK([)', 18, 17],
        ];
    }

    public function providesExceptionSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK(INVALID)', 23],
            ['<?php class SomeClass { }', 'STARTBLOCK({)', 1],
            ['<?php class SomeClass {', 'STARTBLOCK({)', 1],
        ];
    }
}
