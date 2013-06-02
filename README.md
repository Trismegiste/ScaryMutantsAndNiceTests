# Scary Mutants and Nice Tests

A wrapper for PHPUnit to test your tests. Highly experimental.

## What

"Yo dawg, I herd you like tests so I put a mutant in your tests
so u can test your tests" - xzibit

That's the spirit. 

## How

More seriously, this tool makes 3 things :
 * scans all classes in your phpunit-tested package (with a phpunit.xml)
 * intercepts the bootstrap autoloader to make its own loading
 * creates a mutant on the fly to replace the autoloaded class

## Why

Because we can ! In fact it's an effective way to validate if your tests
are doing their job or not. If a mutant passes the tests, it means your test is
not complete, even if you have 100% code-coverage.

Futhermore, objects and classes, like species, evolve and are mutating with time.
A nice unit test is efficient if it can select, like darwinism, the good class
from the buggy (scary) mutants.

## How much

Beware, since there is a parsing and an evaluation of each autoloaded classes,
this could be slow, very slow.

Despite tests on multiple packages, a test could break even without mutant :
 - failure on autoloading
 - files not found because of relative path
 - no PSR-0 compliant
...

## Where

... is the mutant ? Nowhere, it is created by a Visitor on the fly. 
It is in the Visitor folder and is named : "MadScientist".

## When

... to press Ctrl-C ? Well, mutation causes not only failure but also 
immortal creatures. Your tests could infinitly loop and then you have to break it.

## Who

... are the mutants ? At this time, I only alter false to true and true to false.
By modifiying constants' classes to produce mutants, you can tests if future 
minor alterations will break unit tests. Future plans include :
 - default values of properties
 - default values of arguments
 - hard-coded magic number
 - Liskov substitution

## Not invented here

That's right. Other tools already exist but they require php extensions, some
compiling of C modules... The point here is it is full PHP. Even X-Debug is not
required.
