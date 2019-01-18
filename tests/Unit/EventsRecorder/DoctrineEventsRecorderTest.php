<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\EventsRecorder;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\EventsRegistry;
use Dsantang\DomainEventsDoctrine\Aggregator;
use Dsantang\DomainEventsDoctrine\EventsRecorder\DoctrineEventsRecorder;
use Dsantang\DomainEventsDoctrine\Tests\RandomDomainEvent;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use function assert;

final class DoctrineEventsRecorderTest extends TestCase
{
    /**
     * @param DomainEvent[] $domainEvents
     *
     * @dataProvider provideEventAwareChangedEntities
     *
     * @test
     */
    public function onFlushWillAggregateAllTheEventAwareEntities(object $eventAwareEntity, array $domainEvents) : void
    {
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityDeletions')
                   ->willReturn([new Object_()]);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityInsertions')
                   ->willReturn([new Object_()]);

        $unitOfWork->expects(self::once())
                   ->method('getScheduledEntityUpdates')
                   ->willReturn([$eventAwareEntity]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())
                      ->method('getUnitOfWork')
                      ->willReturn($unitOfWork);

        $aggregator = $this->createMock(Aggregator::class);
        assert($aggregator instanceof Aggregator);

        $aggregator->expects(self::once())
                   ->method('aggregate')
                   ->with(...$domainEvents);

        $eventRecorder = new DoctrineEventsRecorder($aggregator);

        $eventArgs = $this->createMock(OnFlushEventArgs::class);
        $eventArgs->expects(self::once())
                  ->method('getEntityManager')
                  ->willReturn($entityManager);

        assert($eventArgs instanceof OnFlushEventArgs);

        $eventRecorder->onFlush($eventArgs);
    }

    /**
     * @return mixed[] array
     */
    public function provideEventAwareChangedEntities() : array
    {
        $awareEntity = new class(new RandomDomainEvent()) implements EventAware {
            use EventsRegistry;

            public function __construct(DomainEvent $domainEvent)
            {
                $this->recordedEvents = [$domainEvent];
            }
        };

        return [
            'with no changed entities' => [new Object_(), []],
            'with a changed entity' => [$awareEntity, [new RandomDomainEvent()]],
        ];
    }
}
