<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class SecondOutboxEntry implements OutboxEntry
{
    private DomainEvent $domainEvent;

    public function __construct(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }

    public function getName(): string
    {
        return 'CartCreated';
    }

    public function getAggregateId(): UuidInterface
    {
        return Uuid::fromString('cece22ea-57ae-11e9-8647-d663bd873d93');
    }

    public function getAggregateType(): string
    {
        return 'Cart';
    }

    public function getPayloadType(): string
    {
        return 'CartType';
    }

    public function getMessageKey(): string
    {
        return 'af422e7a';
    }

    public function getMessageRoute(): string
    {
        return 'snapshot.cart';
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
        return 1;
    }
}
