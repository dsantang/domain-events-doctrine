<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Dispatcher;

use Doctrine\ORM\Event\PostFlushEventArgs;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Dispatcher\DoctrineEventsDispatcher;
use Dsantang\DomainEventsDoctrine\Dispatcher\SymfonyEvent;
use Dsantang\DomainEventsDoctrine\Releaser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function assert;

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
        assert($releaser instanceof Releaser);

        $releaser->expects(self::once())
                 ->method('release')
                 ->willReturn([$domainEvent]);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        assert($dispatcher instanceof EventDispatcherInterface);

        $dispatcher->expects(self::once())
                   ->method('dispatch')
                   ->with(self::EVENT_NAME, self::isInstanceOf(SymfonyEvent::class));

        $eventDispatcher = new DoctrineEventsDispatcher($releaser, $dispatcher);

        $args = $this->createMock(PostFlushEventArgs::class);
        assert($args instanceof PostFlushEventArgs);

        $eventDispatcher->postFlush($args);
    }
}
