<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\Converters;

use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\Converter;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries\ThirdOutboxEntry;

final class ThirdOutboxConverter implements Converter
{
    public function convert(DomainEvent $domainEvent) : OutboxEntry
    {
        return new ThirdOutboxEntry($domainEvent);
    }
}
