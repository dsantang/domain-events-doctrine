<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\EventsTracker;
use PHPUnit\Framework\TestCase;
use function assert;

final class EventsTrackerTest extends TestCase
{
    /** @var EventsTracker */
    private $tracker;

    /** @var DomainEvent[] */
    private $events;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $event1 = $this->createMock(DomainEvent::class);
        assert($event1 instanceof DomainEvent);

        $event2 = $this->createMock(DomainEvent::class);
        assert($event2 instanceof DomainEvent);

        $this->events = [$event1, $event2];

        $this->tracker = new EventsTracker();
    }

    /**
     * @test
     */
    public function settingAggregateShouldCorrectlySetTheProperty() : void
    {
        $this->tracker->aggregate(...$this->events);

        self::assertAttributeSame($this->events, 'events', $this->tracker);
    }

    /**
     * @test
     */
    public function releaseShouldReturnTheEventsAndResetTheInternalState() : void
    {
        $this->tracker->aggregate(...$this->events);

        self::assertSame($this->events, $this->tracker->release());
        self::assertAttributeSame([], 'events', $this->tracker);
    }
}
