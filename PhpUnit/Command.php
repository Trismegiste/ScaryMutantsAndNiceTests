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

    public function __construct($caughtClasses)
    {
        static::$classMap = $caughtClasses;
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

    public static function transformAndEval($class, $filename)
    {
        $parser = static::getParser();
        $stmt = $parser->parse(file_get_contents($filename));

        $traver = new \PHPParser_NodeTraverser();
        $traver->addVisitor(new MadScientist($filename));
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

//    protected function handleArguments(array $argv)
//    {
//        parent::handleArguments($argv);
//        print_r($this->options);
//    }

}