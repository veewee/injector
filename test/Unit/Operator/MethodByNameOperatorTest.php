<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Locator\Operator\MethodByNameOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\MethodByNameOperator
 */
class MethodByNameOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new MethodByNameOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new MethodByNameOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new MethodByNameOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    public function providesOperatesData()
    {
        return [
            ['METHODNAME(mymethod)', true],
            ['METHODNAME(mymethod123)', true],
            ['METHODNAME(mymethod_123)', true],
            ['METHODNAME(mymethod-123)', false],
            ['METHODNAME(12mymethod)', false],
            ['methodname(mymethod)', false],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php (function() {})();', 'METHODNAME(myMethod)', 0, null],
            ['<?php function myMethod() {}', 'METHODNAME(myMethod)', 0, 1],
            ['<?php function myMethod() {} function myMethod2() {}', 'METHODNAME(myMethod)', 0, 1],
            ['<?php function myMethod() {} function myMethod2() {}', 'METHODNAME(myMethod2)', 0, 10],
            ['<?php class X { function myMethod() {} }', 'METHODNAME(myMethod)', 0, 7],
            ['<?php', 'METHODNAME(myMethod)', 0, null],
        ];
    }
}
