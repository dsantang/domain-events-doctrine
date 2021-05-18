<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Dispatcher;

final class SimpleEventsDispatcher extends DoctrineEventsDispatcher
{
    public function postFlush(): void
    {
        $events = $this->releaser->release();

        foreach ($events as $event) {
            $this->dispatcher->dispatch(new SymfonyEvent($event), $event->getName());
        }
    }
}
