<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Dispatcher;

use Dsantang\DomainEvents\DomainEvent;
use Symfony\Component\EventDispatcher\Event;

final class SymfonyEvent extends Event implements DomainEvent
{
    /** @var DomainEvent */
    private $event;

    public function __construct(DomainEvent $event)
    {
        $this->event = $event;
    }

    public function getName() : string
    {
        return $this->event->getName();
    }

    public function getEvent() : DomainEvent
    {
        return $this->event;
    }
}
