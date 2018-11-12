<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Dispatcher;

use Dsantang\DomainEventsDoctrine\Dispatcher\SymfonyEvent;
use Dsantang\DomainEventsDoctrine\Tests\RandomDomainEvent;
use PHPUnit\Framework\TestCase;

final class SymfonyEventTest extends TestCase
{
    /**
     * @test
     */
    public function getNameReturnsCorrectName() : void
    {
        $event = new SymfonyEvent(new RandomDomainEvent());

        self::assertSame(RandomDomainEvent::EVENT_NAME, $event->getName());
    }
}
