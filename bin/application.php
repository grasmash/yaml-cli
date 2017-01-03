<?php

set_time_limit(0);
require __DIR__ . '/../vendor/autoload.php';

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Command\UpdateValueCommand;
use Symfony\Component\Console\Application;

$application = new Application('yaml-cli', '@package_version@');
$application->add(new GetValueCommand());
$application->add(new UpdateValueCommand());
$application->run();
