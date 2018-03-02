<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Console\Style;

use Injector\Console\Style\ConsoleStyle;
use Injector\Exception\InputStreamException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @covers \Injector\Console\Style\ConsoleStyle
 */
class ConsoleStyleTest extends TestCase
{
    public function test_it_is_a_symfony_console_style(): void
    {
        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $this->assertInstanceOf(SymfonyStyle::class, $style);
    }

    public function test_it_throws_exception_on_invalid_resource(): void
    {
        $this->expectException(InputStreamException::class);

        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $style->readResource(new \stdClass());
    }

    public function test_it_throws_exception_on_empty_resource(): void
    {
        $this->expectException(InputStreamException::class);

        $stream = fopen('php://temp', 'rb');
        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $style->readResource($stream);
    }

    public function test_it_throws_exception_on_whitespace_resource(): void
    {
        $this->expectException(InputStreamException::class);

        $stream = fopen('php://temp', 'rwb');
        fwrite($stream, '        ');
        rewind($stream);

        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $style->readResource($stream);
        fclose($stream);
    }

    public function test_it_runs_in_non_blocking_mode(): void
    {
        $stream = fopen(__FILE__, 'rb');
        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $style->readResource($stream);

        $meta = stream_get_meta_data($stream);

        $this->assertFalse($meta['blocked']);
    }

    public function test_it_can_read_a_resource_and_strips_the_last_newline_at_eof(): void
    {
        $stream = fopen('php://temp', 'rwb');
        fwrite($stream, '<?php echo "Hello world"; ');
        rewind($stream);

        $style = new ConsoleStyle(new ArrayInput([]), new ConsoleOutput());
        $input = $style->readResource($stream);
        fclose($stream);

        $this->assertSame('<?php echo "Hello world";', $input);
    }
}
