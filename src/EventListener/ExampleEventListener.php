<?php

namespace App\EventListener;

use App\Event\ExampleEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
readonly class ExampleEventListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ExampleEvent $event): void
    {
        $this->logger->info($event->getName());
    }
}
