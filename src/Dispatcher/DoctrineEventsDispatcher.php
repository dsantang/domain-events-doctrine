<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Dispatcher;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Dsantang\DomainEventsDoctrine\Releaser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DoctrineEventsDispatcher
{
    /** @var Releaser */
    private $releaser;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(Releaser $releaser)
    {
        $this->releaser = $releaser;
    }

    public function postFlush(PostFlushEventArgs $args) : void
    {
        $events = $this->releaser->release();

        foreach ($events as $event) {
            $this->dispatcher->dispatch($event->getName(), new SymfonyEvent($event));
        }
    }
}
