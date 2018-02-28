<?php

declare(strict_types=1);

namespace InjectorTest\Unit;

use Injector\InjectionResult;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @covers \Injector\InjectionResult
 */
class InjectionResultTest extends TestCase
{
    private $file;

    protected function setUp(): void
    {
        $this->file = new SplFileInfo('Somefile.php', 'Somefile.php', 'Somefile.php');
    }

    public function test_it_contains_a_file(): void
    {
        $result = new InjectionResult($this->file);
        $this->assertSame($this->file, $result->getFile());
    }

    public function test_it_does_not_contain_a_diff_by_default(): void
    {
        $result = new InjectionResult($this->file);
        $this->assertFalse($result->hasDiff());
        $this->assertNull($result->getDiff());
    }

    public function test_it_does_not_contain_new_code_by_default(): void
    {
        $result = new InjectionResult($this->file);
        $this->assertFalse($result->hasNewCode());
        $this->assertNull($result->getNewCode());
    }

    public function test_it_does_not_contain_exception_by_default(): void
    {
        $result = new InjectionResult($this->file);
        $this->assertFalse($result->hasException());
        $this->assertNull($result->getException());
    }

    public function test_it_is_possible_to_set_a_diff(): void
    {
        $initialResult = new InjectionResult($this->file);
        $result = $initialResult->withDiff($diff = 'somediff');

        $this->assertNull($initialResult->getDiff());
        $this->assertTrue($result->hasDiff());
        $this->assertSame($diff, $result->getDiff());
    }

    public function test_it_is_possible_to_set_new_code(): void
    {
        $initialResult = new InjectionResult($this->file);
        $result = $initialResult->withNewCode($newCode = 'newCode');

        $this->assertNull($initialResult->getNewCode());
        $this->assertTrue($result->hasNewCode());
        $this->assertSame($newCode, $result->getNewCode());
    }

    public function test_it_is_possible_to_set_exception(): void
    {
        $initialResult = new InjectionResult($this->file);
        $result = $initialResult->withException($exception = new \Exception('oh noes'));

        $this->assertNull($initialResult->getException());
        $this->assertTrue($result->hasException());
        $this->assertSame($exception, $result->getException());
    }

    public function test_it_is_successfull_when_it_contains_new_code_and_no_exception(): void
    {
        $this->assertFalse((new InjectionResult($this->file))->isSuccessfull());
        $this->assertFalse(
            (new InjectionResult($this->file))
                ->withException(new \Exception('oh noes'))
                ->isSuccessfull()
        );
        $this->assertFalse(
            (new InjectionResult($this->file))
                ->withNewCode('newCode')
                ->withException(new \Exception('oh noes'))
                ->isSuccessfull()
        );
        $this->assertTrue(
            (new InjectionResult($this->file))
                ->withNewCode('newCode')
                ->isSuccessfull()
        );
    }
}
