<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use Ramsey\Uuid\UuidInterface;

interface OutboxEntry
{
    public function getMessageKey() : string;

    public function getMessageRoute() : string;

    public function getMessageType() : string;

    public function getAggregateId() : UuidInterface;

    public function getAggregateType() : string;

    public function getPayloadType() : string;

    public function getPayload() : string;

    public function getSchemaVersion() : int;
}
