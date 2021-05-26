<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\EventsTracker;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class EventsTrackerTest extends TestCase
{
    private EventsTracker $tracker;

    /** @var DomainEvent[] */
    private array $events;

    /**
     * @before
     */
    public function setUpDependencies(): void
    {
        $event1 = $this->createMock(DomainEvent::class);
        $event2 = $this->createMock(DomainEvent::class);

        $this->events = [$event1, $event2];

        $this->tracker = new EventsTracker();
    }

    /**
     * @test
     */
    public function settingAggregateShouldCorrectlySetTheProperty(): void
    {
        $this->tracker->aggregate(...$this->events);

        self::assertAttributeSame($this->events, 'events', $this->tracker);
    }

    /**
     * @test
     */
    public function releaseShouldReturnTheEventsAndResetTheInternalState(): void
    {
        $this->tracker->aggregate(...$this->events);

        self::assertSame($this->events, $this->tracker->release());
        self::assertAttributeSame([], 'events', $this->tracker);
    }

    /**
     * @param array<DomainEvent> $expectedDomainEvents
     */
    private static function assertAttributeSame(
        array $expectedDomainEvents,
        string $propertyName,
        EventsTracker $eventsTracker
    ): void {
        $refl = (new ReflectionClass($eventsTracker))->getProperty($propertyName);
        $refl->setAccessible(true);

        self::assertSame($expectedDomainEvents, $refl->getValue($eventsTracker));
    }
}
