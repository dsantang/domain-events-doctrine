<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine;

use Dsantang\DomainEvents\DomainEvent;

interface Aggregator
{
    public function aggregate(DomainEvent ...$events) : void;
}
