<?php

declare(strict_types=1);

namespace CopyPaste\Console\Command;

use CopyPaste\Locator\TokenLocator;
use PhpCsFixer\Linter\Linter;
use PhpCsFixer\Tokenizer\Tokens;
use RuntimeException;
use SebastianBergmann\Diff\Differ;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class CopyPasteCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this->setName('copy-paste');

        $this->addArgument('src', InputArgument::REQUIRED, 'The files you want to copy / paste in');
        $this->addArgument('location', InputArgument::REQUIRED, 'The location where you want to copy / paste');
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dont change the code but print the results to the screen');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $locator = new TokenLocator();
        $differ = new Differ();
        $linter = new Linter();

        $exitCode = 0;

        $files = Finder::create()
            ->files()
            ->in($input->getArgument('src'))
            ->name('*.php')
            ->getIterator();
        $data = $this->fetchTokensFromStream(STDIN);

        foreach ($files as $file) {
            try {
                $sourceCode = $file->getContents();
                $tokens = Tokens::fromCode($sourceCode);
                $index = $locator->locate($tokens, $input->getArgument('location'));
                $tokens->insertAt($index, $data);

                $resultCode = $tokens->generateCode();
                $linter->lintSource($resultCode)->check();

                if (!$input->hasOption('dry-run')) {
                    file_put_contents($file->getPathname(), $resultCode);
                }

                $style->success('Handled ' . $file->getPathname());

            } catch (\Exception $e) {
                $style->error('Could not copy / paste in ' . $file->getPathname() . ': ' . $e->getMessage());



                $exitCode = 1;
                continue;
            } finally {
                if (isset($sourceCode, $resultCode)) {
                    $style->writeln($differ->diff($sourceCode, $resultCode));
                }
            }
        }

        return $exitCode;
    }

    private function fetchTokensFromStream($handle): Tokens
    {
        if (!is_resource($handle)) {
            throw new RuntimeException(
                sprintf('Expected a resource stream for reading the commandline input. Got %s.', gettype($handle))
            );
        }

        $input = '';
        while (!feof($handle)) {
            $input .= fread($handle, 1024);
        }

        // When the input only consist of white space characters, we assume that there is no input.
        $code = !preg_match_all('/^([\s]*)$/', $input) ? $input : '';
        if (!$code) {
            throw new \RuntimeException('No code input detected');
        }

        // Strip automatically added newline
        $code = substr($code, 0, -1);

        return Tokens::fromCode($code);
    }
}
