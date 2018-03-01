<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Operator;

use Injector\Exception\LocationException;
use Injector\Locator\Operator\NewUseLocationOperator;
use Injector\Locator\Operator\TokenOperatorInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Injector\Locator\Operator\NewUseLocationOperator
 */
class NewUseLocationOperatorTest extends TestCase
{
    public function test_it_is_a_token_operator(): void
    {
        $this->assertInstanceOf(TokenOperatorInterface::class, new NewUseLocationOperator());
    }

    /**
     * @dataProvider providesOperatesData
     */
    public function test_it_operates(string $location, bool $expected): void
    {
        $operator = new NewUseLocationOperator();
        $this->assertSame($expected, $operator->operates($location));
    }

    /**
     * @dataProvider providesSearchIndexesData
     */
    public function test_it_searches_indexes(string $code, string $location, int $previousIndex, ?int $expected): void
    {
        $operator = new NewUseLocationOperator();
        $tokens = Tokens::fromCode($code);
        $this->assertSame($expected, $operator->searchIndex($tokens, $previousIndex, $location));
    }

    /**
     * @dataProvider providesExceptionalSearchIndexesData
     */
    public function test_it_throw_exception_when_import_allready_exists(string $code, string $location, int $previousIndex): void
    {
        $this->expectException(LocationException::class);

        $operator = new NewUseLocationOperator();
        $tokens = Tokens::fromCode($code);
        $operator->searchIndex($tokens, $previousIndex, $location);
    }

    public function providesOperatesData()
    {
        return [
            ['NEWUSE(Main)', true],
            ['NEWUSE(Some\Location)', true],
            ['NEWUSE(Some\OtherLocation)', true],
            ['newuse(Main)', false],
            ['INVALID', false],
        ];
    }

    public function providesSearchIndexesData()
    {
        return [
            ['<?php ', 'NEWUSE(Some\Namespace)', 0, 0],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(Some\Namespace)', 0, 21],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(A\A)', 0, 5],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(A\B\C)', 0, 12],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(B)', 0, 12],
        ];
    }

    public function providesExceptionalSearchIndexesData()
    {
        return [
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(A)', 0],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(A\B)', 0],
            ['<?php use A; use A\B; use B\C\D;', 'NEWUSE(B\C\D)', 0],
        ];
    }
}
