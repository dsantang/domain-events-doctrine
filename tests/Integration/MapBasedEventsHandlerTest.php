<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEventsDoctrine\Outbox\MapBased;
use Dsantang\DomainEventsDoctrine\Tests\OutboxSubClass;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\EventArgsProvider;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\Converters\FirstOutboxConverter;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\FirstDomainEvent;
use PHPUnit\Framework\TestCase;

final class MapBasedEventsHandlerTest extends TestCase
{
    use EventArgsProvider;

    /** @var MapBased */
    private $mapBasedEventsHandler;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $this->mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork    = $this->createMock(UnitOfWork::class);
    }

    public function testOutboxHappyPathWorkflowOnFlushEvent() : void
    {
        $this->mapBasedEventsHandler->addConverter(
            FirstDomainEvent::class,
            new FirstOutboxConverter()
        );

        $eventArgs = $this->getEventArgs();

        $this->entityManager->expects(self::once())->method('persist');
        $this->unitOfWork->expects(self::once())->method('computeChangeSets');

        $this->mapBasedEventsHandler->onFlush($eventArgs);
    }
}
