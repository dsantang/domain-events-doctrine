<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\OutboxEvents;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\EventAware;
use function array_filter;
use function array_merge;
use function ksort;

final class OutboxOrderedDoctrineEvents
{
    /** @var OutboxTransformer */
    private $transformer;

    /** @var OutboxEntityPersistence */
    private $entityPersistence;

    public function __construct(
        OutboxTransformer $transformer,
        OutboxEntityPersistence $entityPersistence
    ) {
        $this->transformer       = $transformer;
        $this->entityPersistence = $entityPersistence;
    }

    public function onFlush(OnFlushEventArgs $eventArgs) : void
    {
        $entityManager = $eventArgs->getEntityManager();

        $events = [];

        foreach (self::getEventAwareEntities($entityManager->getUnitOfWork()) as $entity) {
            $events += array_filter($entity->expelRecordedEvents(), static function ($event) {
                return $event instanceof OutboxEvent;
            });
        }

        ksort($events);

        Counter::reset();

        foreach ($events as $event) {
            $this->entityPersistence->persist($this->transformer->transform($event));
        }
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
