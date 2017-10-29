<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

define('TEMP_DIR', __DIR__ . '/../temp/test' . getmypid());
@mkdir(dirname(TEMP_DIR));
\Tester\Helpers::purge(TEMP_DIR);

$configurator = new Nette\Configurator;
$configurator->setDebugMode(false);
$configurator->setTempDirectory(TEMP_DIR);
$configurator->createRobotLoader()
    ->addDirectory(__DIR__ . '/../app')
    ->addDirectory(__DIR__ . '/test')
    ->register();
$configurator->addConfig(__DIR__ . '/../app/config/config.neon');

return $configurator->createContainer();
