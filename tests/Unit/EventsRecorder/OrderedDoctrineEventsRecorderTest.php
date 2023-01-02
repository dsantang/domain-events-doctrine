<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\EventsRecorder;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\EventsRegistry;
use Dsantang\DomainEventsDoctrine\Aggregator;
use Dsantang\DomainEventsDoctrine\EventsRecorder\OrderedDoctrineEventsRecorder;
use Dsantang\DomainEventsDoctrine\Tests\RandomDomainEvent;
use PHPUnit\Framework\TestCase;

final class OrderedDoctrineEventsRecorderTest extends TestCase
{
    /**
     * @param DomainEvent[] $domainEvents
     *
     * @dataProvider provideEventAwareChangedEntities
     *
     * @test
     */
    public function onFlushWillAggregateAllTheEventAwareEntities(object $eventAwareEntity, array $domainEvents): void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityDeletions')
                   ->willReturn([]);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityInsertions')
                   ->willReturn([$eventAwareEntity]);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityUpdates')
                   ->willReturn([$eventAwareEntity]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())
                      ->method('getUnitOfWork')
                      ->willReturn($unitOfWork);

        $aggregator = $this->createMock(Aggregator::class);

        $aggregator->expects(self::once())
                   ->method('aggregate')
                   ->with(...$domainEvents);

        $eventRecorder = new OrderedDoctrineEventsRecorder($aggregator);

        $eventArgs = $this->createMock(OnFlushEventArgs::class);
        $eventArgs->expects(self::once())
                  ->method('getEntityManager')
                  ->willReturn($entityManager);

        Counter::getNext();

        $eventRecorder->onFlush($eventArgs);

        self::assertEquals(0, Counter::getNext());
    }

    /**
     * @return mixed[] array
     */
    public function provideEventAwareChangedEntities(): array
    {
        $awareEntity = new class (new RandomDomainEvent()) implements EventAware {
            use EventsRegistry;

            public function __construct(DomainEvent $domainEvent)
            {
                $this->recordedEvents = [1 => $domainEvent, 0 => $domainEvent];
            }
        };

        $object = new class (){
        };

        return [
            'with no changed entities' => [$object, []],
            'with a changed entity' => [$awareEntity, [new RandomDomainEvent(), new RandomDomainEvent()]],
        ];
    }
}
