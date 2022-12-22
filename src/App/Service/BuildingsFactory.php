<?php
declare(strict_types=1);

namespace App\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class BuildingsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Buildings
    {
        return new Buildings($container->get("doctrine.entity_manager.orm_default"), $container->get(Tags::class));
    }
}
