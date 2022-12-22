<?php
declare(strict_types=1);

namespace App\Roadrunner;

use Mezzio\Application;
use Mezzio\MiddlewareFactory;
use Psr\Container\ContainerInterface;

class ApplicationFactory
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(): Application
    {
        $app     = $this->container->get(Application::class);
        $factory = $this->container->get(MiddlewareFactory::class);

        (require 'config/pipeline.php')($app, $factory, $this->container);
        (require 'config/routes.php')($app, $factory, $this->container);

        return $app;
    }
}
