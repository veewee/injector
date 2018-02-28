<?php

declare(strict_types=1);

namespace Injector\Console\Command;

use Injector\Console\Style\ConsoleStyle;
use Injector\InjectionResult;
use Injector\Injector;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class InjectorCommand extends Command
{
    public const COMMAND_NAME = 'injector';

    /**
     * @var Injector
     */
    private $injector;

    /**
     * @var resource
     */
    private $inputStream;

    public function __construct(Injector $injector, $inputStream)
    {
        parent::__construct();

        $this->injector = $injector;
        $this->inputStream = $inputStream;
    }

    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);

        $this->addArgument('src', InputArgument::REQUIRED, 'The PHP directory you want to copy / paste in');
        $this->addArgument('location', InputArgument::REQUIRED, 'The location where you want to copy / paste');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dont change the code but print the results to the screen');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new ConsoleStyle($input, $output);
        $exitCode = 0;

        $files = Finder::create()
            ->files()
            ->in($input->getArgument('src'))
            ->name('*.php')
            ->getIterator();
        $data = Tokens::fromCode($style->readResource($this->inputStream));

        foreach ($files as $file) {
            $result = $this->injector->inject($file, $data, $input->getArgument('location'));
            if ($result->isSuccessfull() && !$input->getOption('dry-run')) {
                file_put_contents($file->getPathname(), $result->getNewCode());
            }

            $this->printInjectionResult($style, $result);
            $exitCode |= !$result->isSuccessfull();
        }

        return $exitCode;
    }

    private function printInjectionResult(ConsoleStyle $style, InjectionResult $result): void
    {
        $file = $result->getFile();
        if ($result->isSuccessfull()) {
            $style->success('Handled '.$file->getPathname());
        }

        if (!$result->isSuccessfull()) {
            $message = 'Could not copy / paste in '.$file->getPathname();
            $message .= $result->getException() ? $result->getException()->getMessage() : '';
            $style->error($message);
        }

        if ($result->hasDiff()) {
            $style->writeln((string) $result->getDiff());
        }
    }
}
