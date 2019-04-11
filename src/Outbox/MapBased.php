<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use Dsantang\DomainEvents\DomainEvent;
use InvalidArgumentException;
use function class_exists;
use function get_class;
use function sprintf;

final class MapBased extends EventsHandler
{
    /** @var Converter[] */
    private $conversionMap;

    public function addConverter(string $domainEventClass, Converter $converter) : void
    {
        if (! class_exists($domainEventClass)) {
            throw new InvalidArgumentException(
                sprintf('Domain Event \"%s\" does not exist', $domainEventClass)
            );
        }

        $this->conversionMap[$domainEventClass] = $converter;
    }

    /**
     * @return OutboxEntry[]
     *
     * @var DomainEvent[] $domainEvents
     */
    public function convert(DomainEvent ...$domainEvents) : array
    {
        $outboxEntries = [];

        foreach ($domainEvents as $domainEvent) {
            if (! isset($this->conversionMap[get_class($domainEvent)])) {
                continue;
            }

            $converter = $this->conversionMap[get_class($domainEvent)];

            $outboxEntries[] = $converter->convert($domainEvent);
        }

        return $outboxEntries;
    }
}
