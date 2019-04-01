<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests;

use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxEvent;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RandomOutboxEvent implements OutboxEvent
{
    public function getName() : string
    {
        return 'OrderDispatched';
    }

    public function getRoute() : string
    {
        return 'aggregate.order';
    }

    public function getAggregateId() : UuidInterface
    {
        return Uuid::fromString('d1702762-548b-11e9-8647-d663bd873d93');
    }

    public function getAggregateType() : string
    {
        return 'Order';
    }

    public function getPayloadType() : string
    {
        return 'OrderStructure';
    }
}
