<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\EventsRecorder;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEventsDoctrine\Aggregator;

use function array_filter;
use function array_merge;
use function ksort;

final class OrderedDoctrineEventsRecorder
{
    private Aggregator $eventsAggregator;

    public function __construct(Aggregator $eventAggregator)
    {
        $this->eventsAggregator = $eventAggregator;
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $unitOfWork = $eventArgs->getEntityManager()
                                ->getUnitOfWork();

        $events = [];

        foreach (self::getEventAwareEntities($unitOfWork) as $entity) {
            $events += $entity->expelRecordedEvents();
        }

        ksort($events);

        Counter::reset();
        $this->eventsAggregator->aggregate(...$events);
    }

    /**
     * @return EventAware[]
     */
    private static function getEventAwareEntities(UnitOfWork $unitOfWork): array
    {
        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates(),
            $unitOfWork->getScheduledEntityDeletions()
        );

        return array_filter($entities, static fn ($entity): bool => $entity instanceof EventAware);
    }
}
