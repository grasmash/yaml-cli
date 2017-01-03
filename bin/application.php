<?php

set_time_limit(0);

$repo_root = __DIR__ . '/../';
if (file_exists($repo_root . '/vendor/autoload.php')) {
    require_once $repo_root  . '/vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../autoload.php')) {
    require_once __DIR__ . '/../../autoload.php';
}

use Grasmash\YamlCli\Command\GetValueCommand;
use Grasmash\YamlCli\Command\UpdateValueCommand;
use Symfony\Component\Console\Application;

$application = new Application('yaml-cli', '@package_version@');
$application->add(new GetValueCommand());
$application->add(new UpdateValueCommand());
$application->run();
