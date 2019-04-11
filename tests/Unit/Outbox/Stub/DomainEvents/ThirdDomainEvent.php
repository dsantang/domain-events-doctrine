<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents;

use Dsantang\DomainEvents\DomainEvent;

final class ThirdDomainEvent implements DomainEvent
{
    public function getName() : string
    {
        return 'third';
    }
}
