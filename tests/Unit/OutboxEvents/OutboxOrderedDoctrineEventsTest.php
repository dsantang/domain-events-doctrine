<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\OrderedEventRegistry;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxEntityPersistence;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxEvent;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxOrderedDoctrineEvents;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxTransformer;
use Dsantang\DomainEventsDoctrine\Tests\OutboxSubClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class OutboxOrderedDoctrineEventsTest extends TestCase
{
    /** @var OutboxTransformer */
    private $transformer;

    /** @var OutboxEntityPersistence|MockObject */
    private $entityPersistence;

    /** @var OutboxOrderedDoctrineEvents */
    private $recorder;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $this->transformer       = new OutboxTransformer(new OutboxSubClass());
        $this->entityPersistence = $this->createMock(OutboxEntityPersistence::class);
        $this->recorder          = new OutboxOrderedDoctrineEvents($this->transformer, $this->entityPersistence);
    }

    public function testOnFlushOperations() : void
    {
        Counter::reset();

        $event1 = $this->getEventClass('first');
        $event2 = $this->getEventClass('second');
        $event3 = $this->getEventClass('third');

        $entity1 = $this->getEntityClass();
        $entity2 = $this->getEntityClass();
        $entity3 = $this->getEntityClass();

        $entity1->trigger($event1);
        $entity2->trigger($event2);
        $entity3->trigger($event3);

        $unitOfWork = $this->createMock(UnitOfWork::class);

        $unitOfWork->expects(self::once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([$entity1]);

        $unitOfWork->expects(self::once())
            ->method('getScheduledEntityUpdates')
            ->willReturn([]);

        $unitOfWork->expects(self::once())
            ->method('getScheduledEntityDeletions')
            ->willReturn([$entity2, $entity3]);

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $entityManager->expects(self::once())->method('getUnitOfWork')->willReturn($unitOfWork);

        $eventArgs = $this->createMock(OnFlushEventArgs::class);
        $eventArgs->expects(self::once())->method('getEntityManager')->willReturn($entityManager);

        self::assertEquals(3, Counter::getNext());

        $this->entityPersistence->expects(self::exactly(3))
            ->method('persist')
            ->withConsecutive(
                [$this->transformer->transform($event1)],
                [$this->transformer->transform($event2)],
                [$this->transformer->transform($event3)]
            );

        $this->recorder->onFlush($eventArgs);

        self::assertEquals(0, Counter::getNext());
    }

    private function getEventClass(string $reference) : OutboxEvent
    {
        return new class($reference) implements OutboxEvent {
            private $reference;

            public function __construct(string $reference)
            {
                $this->reference = $reference;
            }

            public function getName() : string
            {
                return $this->reference;
            }

            public function getRoute() : string
            {
                return 'route' . $this->reference;
            }

            public function getAggregateId() : UuidInterface
            {
                return Uuid::uuid4();
            }

            public function getAggregateType() : string
            {
                return $this->reference;
            }

            public function getPayloadType() : string
            {
                return $this->reference;
            }
        };
    }

    private function getEntityClass()
    {
        return new class() implements EventAware {
            use OrderedEventRegistry;

            public function trigger(OutboxEvent $event) : void
            {
                $this->triggeredA($event);
            }
        };
    }
}
