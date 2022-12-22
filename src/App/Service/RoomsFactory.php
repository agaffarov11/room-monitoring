<?php
declare(strict_types=1);

namespace App\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class RoomsFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Rooms
    {
        return new Rooms(
            $container->get("doctrine.entity_manager.orm_default"),
            $container->get(Buildings::class),
            $container->get(Tags::class)
        );
    }
}
