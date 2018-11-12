<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Dispatcher;

use Dsantang\DomainEventsDoctrine\Releaser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DoctrineEventsDispatcher
{
    /** @var Releaser */
    private $releaser;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(Releaser $releaser, EventDispatcherInterface $dispatcher)
    {
        $this->releaser   = $releaser;
        $this->dispatcher = $dispatcher;
    }

    public function postFlush() : void
    {
        $events = $this->releaser->release();

        foreach ($events as $event) {
            $this->dispatcher->dispatch($event->getName(), new SymfonyEvent($event));
        }
    }
}
