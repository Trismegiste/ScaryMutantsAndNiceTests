<?php

/*
 * ScaryMutants
 */

namespace Trismegiste\Smant\PhpUnit;

/**
 * Runner is a runner which removes any existing configuration for code coverage
 * because it is not relevant when embedded in this command
 */
class Runner extends \PHPUnit_TextUI_TestRunner
{

    /**
     * Remove any coverage options
     *
     * @param array $arguments
     */
    protected function handleConfiguration(array &$arguments)
    {
        parent::handleConfiguration($arguments);
        foreach ($arguments as $key => $value) {
            if (preg_match('#^coverage#', $key)) {
                unset($arguments[$key]);
            }
        }
        // adding listeners
        //$arguments['listeners'][] =
    }

}