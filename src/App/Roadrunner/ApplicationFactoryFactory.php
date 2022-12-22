<?php
declare(strict_types=1);

namespace App\Roadrunner;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class ApplicationFactoryFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new ApplicationFactory($container);
    }
}
