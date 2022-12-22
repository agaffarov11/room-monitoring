<?php
declare(strict_types=1);

namespace App\Roadrunner;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

class Psr7WorkerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new PSR7Worker(
            Worker::create(),
            new ServerRequestFactory(),
            new StreamFactory(),
            new UploadedFileFactory(),
        );
    }
}
