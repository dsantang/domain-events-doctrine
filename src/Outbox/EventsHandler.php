<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use function array_filter;
use function array_merge;
use function count;
use function ksort;

abstract class EventsHandler
{
    /** @var OutboxMappedSuperclass */
    protected $outboxMappedSuperclass;

    public function __construct(OutboxMappedSuperclass $outboxMappedSuperclass)
    {
        $this->outboxMappedSuperclass = $outboxMappedSuperclass;
    }

    /**
     * @return OutboxEntry[]
     *
     * @var DomainEvent[] $domainEvents
     */
    abstract protected function convert(DomainEvent ...$domainEvents) : array;

    public function onFlush(OnFlushEventArgs $eventArgs) : void
    {
        $domainEvents = $this->getDomainEvents($eventArgs);

        $outboxEvents = $this->convert(...$domainEvents);

        $this->persist($eventArgs->getEntityManager(), ...$outboxEvents);
    }

    /**
     * @return DomainEvent[]
     */
    protected function getDomainEvents(OnFlushEventArgs $eventArgs) : array
    {
        $unitOfWork = $eventArgs->getEntityManager()
            ->getUnitOfWork();

        $events = [];

        foreach (self::getEventAwareEntities($unitOfWork) as $entity) {
            $events += $entity->expelRecordedEvents();
        }

        ksort($events);

        Counter::reset();

        return $events;
    }

    protected function persist(EntityManagerInterface $entityManager, OutboxEntry ...$outboxEntries) : void
    {
        if (count($outboxEntries) <= 0) {
            return;
        }

        $previousEntity = null;

        foreach ($outboxEntries as $outboxEntry) {
            $entity = $this->outboxMappedSuperclass->fromOutboxEntry($outboxEntry, $previousEntity);
            $entityManager->persist($entity);

            $previousEntity = $entity;
        }

        $entityManager->getUnitOfWork()->computeChangeSets();
    }

    /**
     * @return EventAware[]
     */
    private static function getEventAwareEntities(UnitOfWork $unitOfWork) : array
    {
        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates(),
            $unitOfWork->getScheduledEntityDeletions()
        );

        return array_filter($entities, static function ($entity) {
            return $entity instanceof EventAware;
        });
    }
}
