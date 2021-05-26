<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use Dsantang\DomainEvents\DomainEvent;

interface Converter
{
    public function convert(DomainEvent $domainEvent): OutboxEntry;
}
