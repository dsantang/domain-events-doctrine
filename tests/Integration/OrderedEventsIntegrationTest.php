<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\OrderedEventRegistry;
use Dsantang\DomainEventsDoctrine\Dispatcher\SimpleEventsDispatcher;
use Dsantang\DomainEventsDoctrine\Dispatcher\SymfonyEvent;
use Dsantang\DomainEventsDoctrine\EventsRecorder\OrderedDoctrineEventsRecorder;
use Dsantang\DomainEventsDoctrine\EventsTracker;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class OrderedEventsIntegrationTest extends TestCase
{
    /** @var EventsTracker */
    private $tracker;

    /** @var OrderedDoctrineEventsRecorder */
    private $recorder;

    /** @var EventDispatcherInterface|MockObject */
    private $eventDispatcher;

    /** @var SimpleEventsDispatcher */
    private $doctrineDispatcher;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $this->tracker            = new EventsTracker();
        $this->recorder           = new OrderedDoctrineEventsRecorder($this->tracker);
        $this->eventDispatcher    = $this->createMock(EventDispatcherInterface::class);
        $this->doctrineDispatcher = new SimpleEventsDispatcher(
            $this->tracker,
            $this->eventDispatcher
        );
    }

    /**
     * @test
     */
    public function orderInWhichTheEventsHaveBeenRaisedIsPreserved() : void
    {
        $event1 = new class implements DomainEvent {
            public function getName() : string
            {
                return 'first';
            }
        };
        $event2 = new class implements DomainEvent {
            public function getName() : string
            {
                return 'second';
            }
        };
        $event3 = new class implements DomainEvent {
            public function getName() : string
            {
                return 'third';
            }
        };

        $entity1 = new class implements EventAware{
            use OrderedEventRegistry;

            public function trigger(DomainEvent $event) : void
            {
                $this->triggeredA($event);
            }
        };

        $entity2 = new class implements EventAware {
            use OrderedEventRegistry;

            public function trigger(DomainEvent $event) : void
            {
                $this->triggeredA($event);
            }
        };

        $entity1->trigger($event1);
        $entity2->trigger($event2);
        $entity1->trigger($event3);

        $uow = $this->createMock(UnitOfWork::class);

        $uow->expects(self::once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([$entity1]);

        $uow->expects(self::once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([$entity2]);

        $uow->expects(self::once())
            ->method('getScheduledEntityDeletions')
            ->willReturn([]);

        $em = $this->createMock(EntityManagerInterface::class);

        $em->expects(self::once())->method('getUnitOfWork')->willReturn($uow);

        $eventArgs = $this->createMock(OnFlushEventArgs::class);
        $eventArgs->expects(self::once())->method('getEntityManager')->willReturn($em);

        $this->eventDispatcher->/** @scrutinizer ignore-call */expects(self::exactly(3))
                              ->method('dispatch')
                              ->withConsecutive(
                                  ['first', new SymfonyEvent($event1)],
                                  ['second', new SymfonyEvent($event2)],
                                  ['third', new SymfonyEvent($event3)]
                              );

        self::assertEquals(3, Counter::getNext());

        $this->recorder->onFlush($eventArgs);

        self::assertEquals(0, Counter::getNext());

        $this->doctrineDispatcher->postFlush();
    }
}
