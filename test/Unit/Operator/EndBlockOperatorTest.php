<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\EndBlockOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\EndBlockOperator
 */
class EndBlockOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new EndBlockOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new EndBlockOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new EndBlockOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    /**
     * @dataProvider providesExceptionSearchIndexesData
     */
    public function test_it_throws_exceptions_on_invalid_data(string $code, string $location, int $previousIndex): void
    {
        $this->expectException(\Throwable::class);

        $operator = new EndBlockOperator();
        $tokens = Tokens::fromCode($code);
        $operator->searchIndex($tokens, $previousIndex, $location);
    }

    public function providesOperatesData()
    {
        return [
            ['ENDBLOCK(})', true],
            ['ENDBLOCK(])', true],
            ['ENDBLOCK())', true],
            ['endblock())', false],
            ['ENDBLOCK(INVALID)', false],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK(})', 5, 23],
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK())', 10, 11],
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK(})', 13, 21],
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK(])', 17, 18],
        ];
    }

    public function providesExceptionSearchIndexesData()
    {
        return [
            ['<?php class SomeClass { function x() { return []; } }', 'ENDBLOCK(INVALID)', 5],
            ['<?php class SomeClass { }', 'ENDBLOCK(})', 1],
            ['<?php class SomeClass {', 'ENDBLOCK(})', 1],
        ];
    }
}
