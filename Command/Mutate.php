<?php

/*
 * Mondrian
 */

namespace Trismegiste\Smant\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Finder\Finder;
use Trismegiste\Smant\PhpUnit;
use Trismegiste\Smant\Visitor\ClassMapperCollector;

/**
 * Mutate launch phpunit with mutation
 */
class Mutate extends Command
{

    protected $phpfinder;
    protected $dryRun = false;

    protected function configure()
    {
        $this->setName('mutate')
                ->setDescription('Launches tests with mutants')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'))
                ->addOption('random', null, InputOption::VALUE_REQUIRED, 'The random seed to reproduce same conditions')
                ->addOption('dry', null, InputOption::VALUE_NONE, "Runs the tests without mutation : check if this tool doesn't break anything");
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $ignoreDir = $input->getOption('ignore');
        $this->phpfinder = $this->getPhpFinder($directory, $ignoreDir);
        if ($input->hasOption('random')) {
            srand((int) $input->getOption('random'));
        }
        $this->dryRun = (bool) $input->getOption('dry');
    }

    protected function getPhpFinder($directory, $ignoreDir)
    {
        $scan = new Finder();
        $scan->files()
                ->in($directory)
                ->name('*.php')
                ->exclude($ignoreDir);

        return $scan;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        $traverser = new \PHPParser_NodeTraverser();
        $collector = new ClassMapperCollector();
        $traverser->addVisitor($collector);

        $output->writeln(sprintf("Parsing %d files...", $this->phpfinder->count()));
        $classMap = array();
        foreach ($this->phpfinder->getIterator() as $source) {
            $stmt = $parser->parse($source->getContents());
            $traverser->traverse($stmt);
            foreach ($collector->getDeclaredClass() as $declared) {
                $classMap[$declared] = $source->getRealPath();
            }
        }

        $report = new PhpUnit\NullPrinter();
        $packageDir = $input->getArgument('dir');
        chdir($packageDir);
        $cmd = new PhpUnit\Command($classMap, $report, $this->dryRun);
        $ret = $cmd->run(array('-c', $packageDir), false);

        foreach(array_keys($report->getReport()) as $className) {
            $output->writeln("Test case <info>$className</info> is not a complete failure");
        }
    }

}