<?php

/*
 * ScaryMutantsAndNiceTests
 */

namespace Trismegiste\Smant\PhpUnit;

/**
 * NullPrinter is a NullObject printer which listen tests
 */
class NullPrinter extends \PHPUnit_Util_Printer implements \PHPUnit_Framework_TestListener, IncompleteFailure
{

    protected $trackPerTest = array();
    protected $incompleteFailure = array();

    public function write($buffer)
    {

    }

    protected function decreaseForTest($test)
    {
        $this->trackPerTest[get_class($test)]--;
    }

    public function addError(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->decreaseForTest($test);
    }

    public function addFailure(\PHPUnit_Framework_Test $test, \PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        $this->decreaseForTest($test);
    }

    public function addIncompleteTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {

    }

    public function addSkippedTest(\PHPUnit_Framework_Test $test, \Exception $e, $time)
    {
        $this->decreaseForTest($test);
    }

    public function endTest(\PHPUnit_Framework_Test $test, $time)
    {
        if ($this->trackPerTest[get_class($test)] > 0) {
            $this->incompleteFailure[get_class($test)] = true;
        }
    }

    public function endTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {

    }

    public function startTest(\PHPUnit_Framework_Test $test)
    {
        if (!array_key_exists(get_class($test), $this->trackPerTest)) {
            $this->trackPerTest[get_class($test)] = 0;
        }
        $this->trackPerTest[get_class($test)] += count($test);
    }

    public function startTestSuite(\PHPUnit_Framework_TestSuite $suite)
    {

    }

    public function getReport()
    {
        return $this->incompleteFailure;
    }

}