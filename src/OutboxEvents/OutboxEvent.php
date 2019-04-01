<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\OutboxEvents;

use Dsantang\DomainEvents\DomainEvent;
use Ramsey\Uuid\UuidInterface;

interface OutboxEvent extends DomainEvent
{
    public function getRoute() : string;
    public function getAggregateId() : UuidInterface;
    public function getAggregateType() : string;
    public function getPayloadType() : string;
}
