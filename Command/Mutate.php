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

    protected function configure()
    {
        $this->setName('mutate')
                ->setDescription('Launches tests with mutants')
                ->addArgument('dir', InputArgument::REQUIRED, 'The directory to explore')
                ->addOption('ignore', 'i', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Directories to ignore', array('Tests', 'vendor'));
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('dir');
        $ignoreDir = $input->getOption('ignore');
        $this->phpfinder = $this->getPhpFinder($directory, $ignoreDir);
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

        $report=  new PhpUnit\NullPrinter();
        $packageDir = $input->getArgument('dir');
        chdir($packageDir);
        $cmd = new PhpUnit\Command($classMap, $report);
        $ret = $cmd->run(array('-c', $packageDir), false);

        print_r(array_keys($report->getReport()));
    }

}