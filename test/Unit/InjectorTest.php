<?php

declare(strict_types=1);

namespace InjectorTest\Unit;

use Injector\Exception\LocationException;
use Injector\InjectionResult;
use Injector\Injector;
use Injector\Locator\Operator\TokenOperatorInterface;
use Injector\Locator\TokenLocator;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @covers \Injector\Injector
 */
class InjectorTest extends TestCase
{
    /**
     * @var ObjectProphecy|TokenOperatorInterface
     */
    private $tokenOperator;

    /**
     * @var Injector
     */
    private $injector;

    /**
     * @var ObjectProphecy|SplFileInfo
     */
    private $file;

    protected function setUp(): void
    {
        $this->tokenOperator = $this->prophesize(TokenOperatorInterface::class);
        $this->tokenOperator->searchIndex(Argument::cetera())->willReturn(1);
        $this->file = $this->prophesize(SplFileInfo::class);
        $this->file->getContents()->willReturn('<?php');
        $this->injector = new Injector(
            new TokenLocator($this->tokenOperator->reveal()),
            new Differ(),
            new Linter()
        );
    }

    public function test_valid_injection(): void
    {
        $data = Tokens::fromCode(' echo "ok";');
        $result = $this->injector->inject($this->file->reveal(), $data, 'somelocation');

        $this->assertInstanceOf(InjectionResult::class, $result);
        $this->assertTrue($result->isSuccessfull());
        $this->assertSame('<?php echo "ok";', $result->getNewCode());
        $this->assertNotNull($result->getDiff());
    }

    public function test_invalid_token_locations(): void
    {
        $data = Tokens::fromCode(' echo "ok";');
        $this->tokenOperator->searchIndex(Argument::cetera())->willThrow(
            $exception = new LocationException('Oh noes!')
        );
        $result = $this->injector->inject($this->file->reveal(), $data, 'somelocation');

        $this->assertInstanceOf(InjectionResult::class, $result);
        $this->assertFalse($result->isSuccessfull());
        $this->assertNull($result->getNewCode());
        $this->assertNull($result->getDiff());
        $this->assertSame($exception, $result->getException());
    }

    public function test_invalid_diff(): void
    {
        $this->markTestSkipped('Not sure how to trigger a differ issue ...');
    }

    public function test_invalid_linting(): void
    {
        $this->markTestSkipped('The linter is not doing anythng during tests ...');

        return;

        $data = Tokens::fromCode('invalid PHP code ... (');
        $result = $this->injector->inject($this->file->reveal(), $data, 'somelocation');

        $this->assertInstanceOf(InjectionResult::class, $result);
        $this->assertFalse($result->isSuccessfull());
        $this->assertNotNull($result->getNewCode());
        $this->assertNotNull($result->getDiff());
        $this->assertNotNull($result->getException());
    }
}
