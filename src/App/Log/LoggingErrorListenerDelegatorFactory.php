<?php
declare(strict_types=1);

namespace App\Log;

use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class LoggingErrorListenerDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, callable $callback, ?array $options = null)
    {
        $listener = new LoggingErrorListener($container->get(LoggerInterface::class));
        $errorHandler = $callback();
        $errorHandler->attachListener($listener);
        return $errorHandler;
    }
}
