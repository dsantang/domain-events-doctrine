<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine;

use Dsantang\DomainEvents\DomainEvent;

/**
 * Allows a Client to stage domain events that have been raised by the application.
 * It then offers a way to release those events at a second moment in time, in order, for example, to dispatch them.
 */
final class EventsTracker implements Aggregator, Releaser
{
    /** @var DomainEvent[] */
    private $events = [];

    public function aggregate(DomainEvent ...$events) : void
    {
        $this->events = $events;
    }

    /**
     * @return DomainEvent[]
     */
    public function release() : array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}
