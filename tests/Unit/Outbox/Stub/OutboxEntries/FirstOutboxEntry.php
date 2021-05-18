<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class FirstOutboxEntry implements OutboxEntry
{
    private DomainEvent $domainEvent;

    public function __construct(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }

    public function getAggregateId(): UuidInterface
    {
        return Uuid::fromString('d1702762-548b-11e9-8647-d663bd873d93');
    }

    public function getAggregateType(): string
    {
        return 'Order';
    }

    public function getPayloadType(): string
    {
        return 'OrderStructure';
    }

    public function getMessageKey(): string
    {
        return 'd663bd873d93';
    }

    public function getMessageRoute(): string
    {
        return 'aggregate.order';
    }

    public function getMessageType(): string
    {
        return $this->domainEvent->getName();
    }

    /**
     * @return mixed[]
     */
    public function getPayload(): array
    {
        return ['foo' => 'bar'];
    }

    public function getSchemaVersion(): int
    {
        return 5;
    }
}
