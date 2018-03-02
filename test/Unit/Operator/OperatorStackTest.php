<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Exception\LocationException;
use Injector\Locator\Operator\OperatorStack;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use stdClass;
use Throwable;

/**
 * @covers \Injector\Locator\Operator\OperatorStack
 */
class OperatorStackTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new OperatorStack());
    }

    public function test_it_can_create_a_default_operator_stack(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, OperatorStack::getDefaultStack());
    }

    public function test_it_is_possible_to_add_an_operator(): void
    {
        $tokenOperator = $this->prophesize(TokenOperatorInterface::class);
        $operator = new OperatorStack();
        $operator->add($tokenOperator->reveal());

        $this->expectException(Throwable::class);
        $operator->add(new stdClass());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new OperatorStack(...$this->mockOperators());
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new OperatorStack(...$this->mockOperators());
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    /**
     * @dataProvider providesExceptionalSearchIndexesData
     */
    public function test_it_throws_locationexception_during_searches_indexes(string $code, string $location, int $previousIndex): void
    {
        $this->expectException(LocationException::class);
        $operator = new OperatorStack(...$this->mockOperators());
        $tokens = Tokens::fromCode($code);
        $operator->searchIndex($tokens, $previousIndex, $location);
    }

    public function providesOperatesData()
    {
        return [
            ['VALID1', true],
            ['VALID2', true],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php ', 'VALID1', 0, 1],
            ['<?php ', 'VALID2', 0, 2],
        ];
    }

    public function providesExceptionalSearchIndexesData()
    {
        return [
            ['<?php ', 'INVALID', 0],
        ];
    }

    private function mockOperators()
    {
        $operator1 = $this->prophesize(TokenOperatorInterface::class);
        $operator1->operates(Argument::any())->willReturn(false);
        $operator1->operates('VALID1')->willReturn(true);
        $operator1->searchIndex(Argument::type(Tokens::class), 0, 'VALID1')->willReturn(1);

        $operator2 = $this->prophesize(TokenOperatorInterface::class);
        $operator2->operates(Argument::any())->willReturn(false);
        $operator2->operates('VALID2')->willReturn(true);
        $operator2->searchIndex(Argument::type(Tokens::class), 0, 'VALID2')->willReturn(2);

        return [
            $operator1->reveal(),
            $operator2->reveal(),
        ];
    }
}
