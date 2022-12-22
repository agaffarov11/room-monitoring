<?php
declare(strict_types=1);

namespace App\Roadrunner;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Spiral\RoadRunner\Environment;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Jobs\Consumer;
use Throwable;

class Worker
{
    private ContainerInterface $container;
    private Environment $runtime;

    public function __construct(ContainerInterface $container, Environment $runtime)
    {
        $this->container = $container;
        $this->runtime   = $runtime;
    }

    public function isJobsWorker(): bool
    {
        return $this->runtime->getMode() === Environment\Mode::MODE_JOBS;
    }

    public function isHttpWorker(): bool
    {
        return $this->runtime->getMode() === Environment\Mode::MODE_HTTP;
    }

    public function run(): never
    {
        if ($this->isJobsWorker()) {
            $this->runJobsConsumer();
        }

        if ($this->isHttpWorker()) {
            $this->runHttpRequestsConsumer();
        }

        throw new RuntimeException("Invalid environment");
    }

    private function runJobsConsumer(): never
    {
        /** @var Consumer $consumer */
        $consumer = $this->container->get(Consumer::class);

        while (true) {
            $task = $consumer->waitTask();
            //
            var_dump($task->getName());
            //var_dump(json_encode($task->getPayload()));

            var_dump($task->getId());

            var_dump("=========================================");
            //
            $task->complete();
        }
    }

    private function runHttpRequestsConsumer(): never
    {
        /** @var ApplicationFactory $factory */
        $factory = $this->container->get(ApplicationFactory::class);
        $app     = $factory();
        $worker  = $this->container->get(PSR7Worker::class);

        while (true) {
            $request = $worker->waitRequest();

            try {
                if (!($request instanceof ServerRequestInterface)) {
                    break;
                }

                $response = $app->handle($request);

                $worker->respond($response);
            } catch (Throwable $throwable) {
                $worker->getWorker()->error((string) $throwable);
            }
        }

        exit(0);
    }
}
