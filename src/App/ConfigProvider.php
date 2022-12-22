<?php

declare(strict_types=1);

namespace App;

use App\Roadrunner\Worker;
use App\Roadrunner\WorkerFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Stratigility\Middleware\ErrorHandler;
use Psr\Log\LoggerInterface;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Jobs\Consumer;
use Spiral\RoadRunner\Logger;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.laminas.dev/laminas-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates' => $this->getTemplates(),
        ];
    }

    /**
     * Returns the container dependencies
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [
                Handler\PingHandler::class => Handler\PingHandler::class,
            ],
            'factories' => [
                Handler\HomePageHandler::class => Handler\HomePageHandlerFactory::class,
                Logger::class => InvokableFactory::class,
                Consumer::class => InvokableFactory::class,
                PSR7Worker::class => Roadrunner\Psr7WorkerFactory::class,
                Roadrunner\ApplicationFactory::class => Roadrunner\ApplicationFactoryFactory::class,
                Worker::class => WorkerFactory::class,
                Service\Rooms::class => Service\RoomsFactory::class,
                Service\Buildings::class => Service\BuildingsFactory::class,
                Service\Cameras::class => Service\CamerasFactory::class,
                Service\Tags::class => Service\TagsFactory::class,
            ],
            'aliases' => [
                LoggerInterface::class => Logger::class
            ],
            'delegators' => [
                ErrorHandler::class => [Log\LoggingErrorListenerDelegatorFactory::class],
            ],
        ];
    }

    /**
     * Returns the templates configuration
     */
    public function getTemplates(): array
    {
        return [
            'paths' => [
                'app' => ['templates/app'],
                'error' => ['templates/error'],
                'layout' => ['templates/layout'],
            ],
        ];
    }
}
