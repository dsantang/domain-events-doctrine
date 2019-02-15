<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Dispatcher;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Dispatcher\SimpleEventsDispatcher;
use Dsantang\DomainEventsDoctrine\Dispatcher\SymfonyEvent;
use Dsantang\DomainEventsDoctrine\Releaser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class DoctrineEventsDispatcherTest extends TestCase
{
    private const EVENT_NAME = 'OrderDispatched';

    /**
     * @test
     */
    public function postFlushShouldDispatchAllEvents() : void
    {
        $domainEvent = $this->createMock(DomainEvent::class);
        $domainEvent->expects(self::once())
                    ->method('getName')
                    ->willReturn(self::EVENT_NAME);

        $releaser = $this->createMock(Releaser::class);

        $releaser->expects(self::once())
                 ->method('release')
                 ->willReturn([$domainEvent]);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);

        $dispatcher->expects(self::once())
                   ->method('dispatch')
                   ->with(self::EVENT_NAME, self::isInstanceOf(SymfonyEvent::class));

        $eventDispatcher = new SimpleEventsDispatcher($releaser, $dispatcher);

        $eventDispatcher->postFlush();
    }
}
