<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\EventsHandler;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;

final class StubMapBased extends EventsHandler
{
    /**
     * @return DomainEvent[] array
     */
    public function getDomainEvents(OnFlushEventArgs $eventArgs): array
    {
        return parent::getDomainEvents($eventArgs);
    }

    public function persist(EntityManagerInterface $entityManager, OutboxEntry ...$outboxEntries): void
    {
        parent::persist($entityManager, ...$outboxEntries);
    }

    /**
     * @return OutboxEntry[]
     *
     * @var DomainEvent[] $domainEvents
     */
    protected function convert(DomainEvent ...$domainEvents): array
    {
        return [];
    }
}
