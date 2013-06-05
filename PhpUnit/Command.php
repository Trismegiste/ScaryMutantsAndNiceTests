<?php

/*
 * Scary Mutants and Nice Tests
 */

namespace Trismegiste\Smant\PhpUnit;

use Trismegiste\Smant\Visitor\MadScientist;

/**
 * Command is a wrapper for lauching phpunit
 */
class Command extends \PHPUnit_TextUI_Command
{

    public static $classMap = array();
    public static $callLink = array();
    protected static $parser = null;
    protected static $printer = null;
    protected static $dryRun;
    protected $report;

    public function __construct($caughtClasses, \PHPUnit_TextUI_ResultPrinter $report, $dryRun)
    {
        static::$classMap = $caughtClasses;
        static::$dryRun = $dryRun;
        $this->report = $report;
    }

    public static function getParser()
    {
        if (is_null(static::$parser)) {
            static::$parser = new \PHPParser_Parser(new \PHPParser_Lexer());
        }

        return static::$parser;
    }

    public static function getPrinter()
    {
        if (is_null(static::$printer)) {
            static::$printer = new \PHPParser_PrettyPrinter_Default();
        }
        return static::$printer;
    }

    /**
     * Do the autoloading (with on-the-fly mutation by the MadScientist visitor)
     *
     * @param string $class the FQCN of the class
     * @param string $filename the path to the file
     */
    public static function transformAndEval($class, $filename)
    {
        $stmt = static::getParser()->parse(file_get_contents($filename));

        $traver = new \PHPParser_NodeTraverser();
        $traver->addVisitor(new MadScientist($filename, static::$dryRun));
        $changed = $traver->traverse($stmt);

        $newContent = static::getPrinter()->prettyPrint($changed);
//        echo $newContent;
        eval($newContent);
    }

    protected function handleBootstrap($filename)
    {
        spl_autoload_register(function($class) {
                    if (array_key_exists($class, Command::$classMap)) {
                        Command::transformAndEval($class, Command::$classMap[$class]);
                    }
                }, true);

        parent::handleBootstrap($filename);
    }

    /**
     * My own runner to tune the configuration
     *
     * @return \Trismegiste\Smant\PhpUnit\Runner
     */
    protected function createRunner()
    {
        return new Runner($this->arguments['loader'], $this->report);
    }

}