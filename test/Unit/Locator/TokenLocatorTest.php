<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Locator;

use Injector\Exception\LocationException;
use Injector\Locator\Operator\TokenOperatorInterface;
use Injector\Locator\TokenLocator;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \Injector\Locator\TokenLocator
 */
class TokenLocatorTest extends TestCase
{
    /**
     * @var ObjectProphecy|TokenOperatorInterface
     */
    private $tokenOperator;

    public function setUp(): void
    {
        $this->tokenOperator = $this->prophesize(TokenOperatorInterface::class);
    }

    public function test_it_can_locate_token_index_by_single_location(): void
    {
        $tokens = Tokens::fromCode('<?php class X {}');
        $locator = new TokenLocator($this->tokenOperator->reveal());
        $this->tokenOperator->searchIndex($tokens, 0, 'T_CLASS')->willReturn(1);

        $this->assertSame(1, $locator->locate($tokens, 'T_CLASS'));
    }

    public function test_it_can_locate_token_index_by_multiple_locations(): void
    {
        $tokens = Tokens::fromCode('<?php class X {}');
        $locator = new TokenLocator($this->tokenOperator->reveal());
        $this->tokenOperator->searchIndex($tokens, 0, 'T_CLASS')->willReturn(1);
        $this->tokenOperator->searchIndex($tokens, 1, '{')->willReturn(2);

        $this->assertSame(2, $locator->locate($tokens, 'T_CLASS {'));
    }

    public function test_it_knows_when_a_location_token_cant_be_found(): void
    {
        $tokens = Tokens::fromCode('<?php class X {}');
        $locator = new TokenLocator($this->tokenOperator->reveal());
        $this->tokenOperator->searchIndex($tokens, 0, 'T_CLASS')->willReturn(1);
        $this->tokenOperator->searchIndex($tokens, 1, '{')->willReturn(null);

        $this->expectExceptionObject(LocationException::fromToken('{'));
        $locator->locate($tokens, 'T_CLASS {');
    }

    public function test_it_knows_when_a_location_string_cant_be_found(): void
    {
        $tokens = Tokens::fromCode('<?php class X {}');
        $locator = new TokenLocator($this->tokenOperator->reveal());

        $this->expectExceptionObject(LocationException::fromFullLocation(''));
        $locator->locate($tokens, '');
    }
}
