<?php

namespace Trismegiste\Smant;

require_once 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Trismegiste\Smant\Command;

$info = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json'));

/*
 * Init application
 */
$application = new Application('Scary Mutants and Nice Tests', $info->version);
$application->addCommands(array(
    new Command\Mutate()
));
$application->run();