<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests;

use Dsantang\DomainEvents\DomainEvent;

final class RandomDomainEvent implements DomainEvent
{
    public const EVENT_NAME = 'OrderDispatched';

    public function getName() : string
    {
        return self::EVENT_NAME;
    }
}
