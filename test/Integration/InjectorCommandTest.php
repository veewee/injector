<?php

declare(strict_types=1);

namespace InjectorTest\Integration;

use Injector\Console\ApplicationFactory;
use Injector\Console\Command\InjectorCommand;
use Injector\Exception\InputStreamException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use VirtualFileSystem\FileSystem;

class InjectorCommandTest extends TestCase
{
    /**
     * @var Differ
     */
    private $differ;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    public function setUp(): void
    {
        $this->differ = new Differ();
        $this->fileSystem = new FileSystem();
        $this->fileSystem->createDirectory('/src');

        file_put_contents($this->fileSystem->path('/src/helloworld.php'), '<?php ');
    }

    public function test_it_crashes_on_empty_input(): void
    {
        $this->expectExceptionObject(InputStreamException::fromEmptyStdIn());

        $tester = $this->createCommandTester('');
        $tester->execute([
            'src' => $this->fileSystem->path('/src'),
            'location' => 'T_CLASS',
        ]);
    }

    public function test_it_crashes_on_empty_src(): void
    {
        $this->expectException(RuntimeException::class);

        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([]);
    }

    public function test_it_crashes_on_empty_location(): void
    {
        $this->expectException(RuntimeException::class);

        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => $this->fileSystem->path('/src'),
        ]);
    }

    public function test_it_crashes_on_invalid_src(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => '/DOESNOTEXIST',
            'location' => 'T_CLASS',
        ]);
    }

    public function test_it_returns_errorcode_on_invalid_location(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => '/src/helloworld.php',
            'location' => 'INVALIDTOKEN',
        ]);

        $file = $this->fileSystem->path('/src/helloworld.php');
        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertContains('Could not copy / paste in '.$file, $tester->getDisplay());
        $this->assertSame('<?php ', file_get_contents($file));
    }

    public function test_it_returns_errorcode_on_tokens_that_could_not_be_found(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => '/src/helloworld.php',
            'location' => 'T_CLASS',
        ]);

        $file = $this->fileSystem->path('/src/helloworld.php');
        $this->assertEquals(1, $tester->getStatusCode());
        $this->assertContains('Could not copy / paste in '.$file, $tester->getDisplay());
        $this->assertSame('<?php ', file_get_contents($file));
    }

    public function test_it_writes_copy_pasted_data_to_file(): void
    {
        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => $this->fileSystem->path('/src'),
            'location' => '>',
        ]);

        $file = $this->fileSystem->path('/src/helloworld.php');
        $expectedCode = '<?php echo "Hello world";';
        $expectedDiff = $this->differ->diff('<?php ', $expectedCode);

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertContains('Handled '.$file, $tester->getDisplay());
        $this->assertContains($expectedDiff, $tester->getDisplay());
        $this->assertSame($expectedCode, file_get_contents($file));
    }

    public function test_it_writes_copy_pasted_data_to_screen_during_dry_run(): void
    {
        $tester = $this->createCommandTester('echo "Hello world";');
        $tester->execute([
            'src' => $this->fileSystem->path('/src'),
            'location' => '>',
            '--dry-run' => true,
        ]);

        $file = $this->fileSystem->path('/src/helloworld.php');
        $expectedCode = '<?php echo "Hello world";';
        $expectedDiff = $this->differ->diff('<?php ', $expectedCode);

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertContains('Handled '.$file, $tester->getDisplay());
        $this->assertContains($expectedDiff, $tester->getDisplay());
        $this->assertSame('<?php ', file_get_contents($file));
    }

    private function createCommandTester(string $inputData): CommandTester
    {
        $input = fopen('php://memory', 'rwb');
        fwrite($input, $inputData.PHP_EOL);
        rewind($input);

        $application = ApplicationFactory::create($input);

        $command = $application->find(InjectorCommand::COMMAND_NAME);

        return new CommandTester($command);
    }
}
