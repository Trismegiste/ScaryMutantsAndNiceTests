<?php

/*
 * ScaryMutants
 */

namespace Trismegiste\Smant\PhpUnit;

/**
 * InverseResult is a ...
 *
 * @author florent
 */
class InverseResult extends \PHPUnit_TextUI_ResultPrinter
{

  

    public function printResult(\PHPUnit_Framework_TestResult $result)
    {
        foreach ($result->passed() as $name => $success) {
            echo $name . PHP_EOL;
        }
    }

}