<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine;

use Dsantang\DomainEvents\DomainEvent;

interface Releaser
{
    /**
     * @return DomainEvent[]
     */
    public function release() : array;
}
