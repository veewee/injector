<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Console;

use Injector\Console\ApplicationFactory;
use Injector\Console\Command\InjectorCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;

/**
 * @covers \Injector\Console\ApplicationFactory
 */
class ApplicationFactoryTest extends TestCase
{
    public function test_it_can_create_a_console_application(): void
    {
        $handle = fopen('php://temp', 'rb');
        $application = ApplicationFactory::create($handle);

        $this->assertInstanceOf(Application::class, $application);
        $this->assertInstanceOf(
            InjectorCommand::class,
            $application->find(InjectorCommand::COMMAND_NAME)
        );
        fclose($handle);
    }
}
