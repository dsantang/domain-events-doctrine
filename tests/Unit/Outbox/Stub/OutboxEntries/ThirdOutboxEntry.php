<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class ThirdOutboxEntry implements OutboxEntry
{
    /** @var DomainEvent */
    private $domainEvent;

    public function __construct(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }

    public function getName() : string
    {
        return 'CustomerDeleted';
    }

    public function getAggregateId() : UuidInterface
    {
        return Uuid::fromString('49ca1f44-56ec-11e9-8647-d663bd873d93');
    }

    public function getAggregateType() : string
    {
        return 'CustomerType';
    }

    public function getPayloadType() : string
    {
        return 'CustomerDetails';
    }

    public function getMessageKey() : string
    {
        return 'ba70d882';
    }

    public function getMessageRoute() : string
    {
        return 'event.customer';
    }

    public function getMessageType() : string
    {
        return $this->domainEvent->getName();
    }

    /**
     * @return mixed[]
     */
    public function getPayload() : array
    {
        return ['foo' => 'bar'];
    }

    public function getSchemaVersion() : int
    {
        return 2;
    }
}
