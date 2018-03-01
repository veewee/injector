<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\ParserTokenOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\ParserTokenOperator
 */
class ParserTokenOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new ParserTokenOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new ParserTokenOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new ParserTokenOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        $constants = get_defined_constants(true);
        $parserConstants = $constants['tokenizer'] ?? [];

        return array_merge(
            [
                ['INVALID', false],
                ['TOKEN_PARSE', false],
            ],
            array_map(
                function ($constantName) {
                    return [$constantName, true];
                },
                array_filter(
                    array_keys($parserConstants),
                    function ($name) {
                        return (bool) preg_match('/^T_/', $name);
                    }
                )
            )
        );
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php class X {}', 'T_CLASS', 0, 1],
            ['<?php function X() {}', 'T_FUNCTION', 0, 1],
        ];
    }
}
