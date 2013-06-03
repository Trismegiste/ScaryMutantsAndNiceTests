<?php

/*
 * ScaryMutantsAndNiceTests
 */

namespace Trismegiste\Smant\PhpUnit;

/**
 * NullPrinter is a NullObject printer which listen tests
 */
class NullPrinter extends \PHPUnit_Util_Printer implements \PHPUnit_Framework_TestListener
{

    protected $expected;

    public function __construct($out = NULL)
    {
        parent::__construct($out);
        $this->expected = new \SplObjectStorage();
    }

    public function write($buffer)
    {
        
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        foreach($this->expected as $cpt) {
            $cpt->setInfo($cpt->getInfo() - 1);
        }
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->expected[$test] = $this->expected[$test] - 1;
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        
    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        //    if ($this->expected[$test] > 0) {
        printf("%s : %d / %d\n", get_class($test), $this->expected[$test], count($test));
        //  }
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
//        if ($this->expected[$suite] > 0) {
        printf("ts %s : %d / %d\n", get_class($suite), $this->expected[$suite], count($suite));
        //      }
    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
        printf("%s : %d\n", get_class($test), count($test));
        $this->expected[$test] = count($test);
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {
        printf("ts %s : %d\n", get_class($suite), count($suite));
        $this->expected[$suite] = count($suite);
    }

}