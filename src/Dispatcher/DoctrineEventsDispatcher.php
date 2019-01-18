<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Dispatcher;

use Dsantang\DomainEventsDoctrine\Releaser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class DoctrineEventsDispatcher
{
    /** @var Releaser */
    protected $releaser;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    public function __construct(Releaser $releaser, EventDispatcherInterface $dispatcher)
    {
        $this->releaser   = $releaser;
        $this->dispatcher = $dispatcher;
    }

    abstract public function postFlush() : void;
}
