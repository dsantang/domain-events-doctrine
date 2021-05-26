<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Dsantang\DomainEventsDoctrine\Outbox\Converter;
use Dsantang\DomainEventsDoctrine\Outbox\MapBased;
use Dsantang\DomainEventsDoctrine\Tests\OutboxSubClass;
use Dsantang\DomainEventsDoctrine\Tests\RandomDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\Converters\FirstOutboxConverter;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\Converters\SecondOutboxConverter;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\Converters\ThirdOutboxConverter;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\FirstDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\SecondDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\ThirdDomainEvent;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

use function array_pop;

final class MapBasedTest extends TestCase
{
    use EventArgsProvider;

    /** @var Converter[] */
    private array $conversionMap;

    /**
     * @before
     */
    public function setUpDependencies(): void
    {
        $this->conversionMap = [
            FirstDomainEvent::class  => new FirstOutboxConverter(),
            SecondDomainEvent::class => new SecondOutboxConverter(),
            ThirdDomainEvent::class  => new ThirdOutboxConverter(),
        ];

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork    = $this->createMock(UnitOfWork::class);
    }

    public function testDealWithInvalidKeyConversionMap(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        $mapBasedEventsHandler->addConverter(
            'Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\NonExistentDomainEvent',
            new FirstOutboxConverter()
        );
    }

    public function testConvert(): void
    {
        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        foreach ($this->conversionMap as $domainEventClassName => $converter) {
            $mapBasedEventsHandler->addConverter($domainEventClassName, $converter);
        }

        $domainEvents = [new ThirdDomainEvent(), new FirstDomainEvent(), new SecondDomainEvent()];

        $returnedOutboxEvents = $mapBasedEventsHandler->convert(...$domainEvents);

        $expectedOutboxEvents = [
            (new ThirdOutboxConverter())->convert(new ThirdDomainEvent()),
            (new FirstOutboxConverter())->convert(new FirstDomainEvent()),
            (new SecondOutboxConverter())->convert(new SecondDomainEvent()),
        ];

        self::assertEquals($expectedOutboxEvents, $returnedOutboxEvents);
    }

    public function testConvertWithANonOutboxEntryDomainEvent(): void
    {
        array_pop($this->conversionMap);
        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        foreach ($this->conversionMap as $domainEventClassName => $converter) {
            $mapBasedEventsHandler->addConverter($domainEventClassName, $converter);
        }

        $domainEvents = [new FirstDomainEvent(), new SecondDomainEvent(), new ThirdDomainEvent()];

        $returnedOutboxEvents = $mapBasedEventsHandler->convert(...$domainEvents);

        $expectedOutboxEvents = [
            (new FirstOutboxConverter())->convert(new FirstDomainEvent()),
            (new SecondOutboxConverter())->convert(new SecondDomainEvent()),
        ];

        self::assertEquals($expectedOutboxEvents, $returnedOutboxEvents);
    }

    public function testConvertANonOutboxRelatedDomainEvent(): void
    {
        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        foreach ($this->conversionMap as $domainEventClassName => $converter) {
            $mapBasedEventsHandler->addConverter($domainEventClassName, $converter);
        }

        $domainEvents = [new RandomDomainEvent(), new SecondDomainEvent()];

        $returnedOutboxEvents = $mapBasedEventsHandler->convert(...$domainEvents);

        $expectedOutboxEvents = [(new SecondOutboxConverter())->convert(new SecondDomainEvent())];

        self::assertEquals($expectedOutboxEvents, $returnedOutboxEvents);
    }

    public function testOnFlushWithDomainEvents(): void
    {
        $eventArgs = $this->getEventArgs();

        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        foreach ($this->conversionMap as $domainEventClassName => $converter) {
            $mapBasedEventsHandler->addConverter($domainEventClassName, $converter);
        }

        $this->entityManager->expects(self::exactly(3))->method('persist');

        $mapBasedEventsHandler->onFlush($eventArgs);
    }

    public function testOnFlushWithNoDomainEvents(): void
    {
        $eventArgs = $this->getEventArgs(false);

        $mapBasedEventsHandler = new MapBased(new OutboxSubClass());

        foreach ($this->conversionMap as $domainEventClassName => $converter) {
            $mapBasedEventsHandler->addConverter($domainEventClassName, $converter);
        }

        $this->unitOfWork->expects(self::never())->method('computeChangeSets');

        $mapBasedEventsHandler->onFlush($eventArgs);
    }
}
