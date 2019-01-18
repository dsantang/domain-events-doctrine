<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\EventsRecorder;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEventsDoctrine\Aggregator;
use function array_filter;
use function array_merge;

/**
 * Doctrine's listener in charge of staging the domain events before the flush operation is completed.
 */
final class DoctrineEventsRecorder
{
    /** @var Aggregator */
    private $eventAggregator;

    public function __construct(Aggregator $eventAggregator)
    {
        $this->eventAggregator = $eventAggregator;
    }

    public function onFlush(OnFlushEventArgs $eventArgs) : void
    {
        $unitOfWork = $eventArgs->getEntityManager()
                                ->getUnitOfWork();

        $events = [];

        foreach (self::getEventAwareEntities($unitOfWork) as $entity) {
            $events = array_merge($events, $entity->expelRecordedEvents());
        }

        $this->eventAggregator->aggregate(...$events);
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
