<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use Dsantang\DomainEvents\DomainEvent;
use InvalidArgumentException;

use function class_exists;
use function sprintf;

final class MapBased extends EventsHandler
{
    /** @var Converter[] */
    private array $conversionMap;

    public function addConverter(string $domainEventClass, Converter $converter): void
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
     */
    public function convert(DomainEvent ...$domainEvents): array
    {
        $outboxEntries = [];

        foreach ($domainEvents as $domainEvent) {
            if (! isset($this->conversionMap[$domainEvent::class])) {
                continue;
            }

            $converter = $this->conversionMap[$domainEvent::class];

            $outboxEntries[] = $converter->convert($domainEvent);
        }

        return $outboxEntries;
    }
}
