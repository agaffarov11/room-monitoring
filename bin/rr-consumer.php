<?php

declare(strict_types=1);

chdir(dirname(__DIR__));

require 'vendor/autoload.php';

/** @var \Psr\Container\ContainerInterface $container */
$container = require 'config/container.php';
/** @var \App\Roadrunner\Worker $worker */
$worker    = $container->get(\App\Roadrunner\Worker::class);

$worker->run();
