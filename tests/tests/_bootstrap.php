<?php declare(strict_types=1);

use Tracy\Debugger;

require __DIR__ . '/../vendor/autoload.php';

$loader = new Nette\Loaders\RobotLoader;
$loader->addDirectory(__DIR__ . '/../app');
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__ . '/../temp'));
$loader->setTempDirectory(__DIR__ . '/../temp');
$loader->register();

if (!isset($_SERVER['HTTP_HOST']) && PHP_SAPI === 'cli') {
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
}
