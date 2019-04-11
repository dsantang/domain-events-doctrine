<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEvents\Counter;
use Dsantang\DomainEventsDoctrine\Tests\OutboxSubClass;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\FirstDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\SecondDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\ThirdDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries\FirstOutboxEntry;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries\SecondOutboxEntry;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\OutboxEntries\ThirdOutboxEntry;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\StubMapBased;
use PHPUnit\Framework\TestCase;
use function array_values;

final class StubMapBasedTest extends TestCase
{
    use EventArgsProvider;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork    = $this->createMock(UnitOfWork::class);
    }

    public function testGetDomainsEvents() : void
    {
        Counter::reset();

        $eventArgs = $this->getEventArgs();

        self::assertEquals(3, Counter::getNext());

        $mapBasedEventsHandler = new StubMapBased(new OutboxSubClass());

        $eventsResult = $mapBasedEventsHandler->getDomainEvents($eventArgs);

        self::assertEquals(0, Counter::getNext());

        $eventsExpected = [new FirstDomainEvent(), new SecondDomainEvent(), new ThirdDomainEvent()];

        self::assertEquals(array_values($eventsResult), array_values($eventsExpected));
    }

    /**
     * @return mixed[] array
     */
    public function persistDataProvider() : array
    {
        return [
            [[], 0, 0],
            [[new FirstOutboxEntry(new FirstDomainEvent())], 1, 1],
            [
                [
                    new ThirdOutboxEntry(new ThirdDomainEvent()),
                    new FirstOutboxEntry(new FirstDomainEvent()),
                    new SecondOutboxEntry(new SecondDomainEvent()),
                ], 3,
                1,
            ],
        ];
    }

    /**
     * @param mixed[] $outboxEvents
     *
     * @dataProvider persistDataProvider
     */
    public function testPersist(array $outboxEvents, int $persistCalls, int $computeChangeSetsCalls) : void
    {
        $eventArgs = $this->getEventArgs();

        $mapBasedEventsHandler = new StubMapBased(new OutboxSubClass());

        $this->entityManager->expects(self::exactly($persistCalls))->method('persist');

        $this->unitOfWork->expects(self::exactly($computeChangeSetsCalls))->method('computeChangeSets');

        $mapBasedEventsHandler->persist($eventArgs->getEntityManager(), ...$outboxEvents);
    }
}
