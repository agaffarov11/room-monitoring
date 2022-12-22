<?php
declare(strict_types=1);

namespace App\Roadrunner;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Spiral\RoadRunner\Environment;

class WorkerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Worker
    {
        $env = Environment::fromGlobals();

        return new Worker($container, $env);
    }
}
