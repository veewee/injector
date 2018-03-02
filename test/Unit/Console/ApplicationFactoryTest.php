<?php

declare(strict_types=1);

namespace InjectorTest\Unit\Console;

use Injector\Console\ApplicationFactory;
use Injector\Console\Command\InjectorCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * @covers \Injector\Console\ApplicationFactory
 */
class ApplicationFactoryTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $handle = fopen('php://temp', 'rb');
        $this->application = ApplicationFactory::create($handle);
    }

    public function test_it_can_create_a_console_application(): void
    {
        $this->assertInstanceOf(Application::class, $this->application);
        $this->assertInstanceOf(
            InjectorCommand::class,
            $this->application->find(InjectorCommand::COMMAND_NAME)
        );
    }

    public function test_it_runs_in_single_command_mode(): void
    {
        $this->application->setAutoExit(false);
        $applicationTester = new ApplicationTester($this->application);
        $applicationTester->run([
            'list',
        ]);

        $this->assertEquals(1, $applicationTester->getStatusCode());
        $this->assertContains('Not enough arguments', $applicationTester->getDisplay());
    }
}
