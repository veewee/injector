<?php

declare(strict_types=1);

namespace Injector\Console;

use Injector\Injector;
use Injector\Locator\Operator\OperatorStack;
use Injector\Locator\TokenLocator;
use PhpCsFixer\Linter\Linter;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Application;

class ApplicationFactory
{
    /**
     * @param resource $inputStream
     */
    public static function create($inputStream): Application
    {
        $app = new Application();
        $app->add(new Command\InjectorCommand(
            new Injector(
                new TokenLocator(OperatorStack::getDefaultStack()),
                new Differ(),
                new Linter()
            ),
            $inputStream
        ));
        $app->setDefaultCommand(Command\InjectorCommand::COMMAND_NAME, true);

        return $app;
    }
}
