<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\OutboxEvents;

use DateTimeImmutable;
use Ramsey\Uuid\Uuid;
use ReflectionClass;

final class OutboxTransformer
{
    /** @var OutboxMappedSuperclass */
    private $entity;

    public function __construct(OutboxMappedSuperclass $entity)
    {
        $this->entity = $entity;
    }

    public function transform(OutboxEvent $event) : OutboxMappedSuperclass
    {
        return $this->entity
            ->setId(Uuid::uuid4())
            ->setCreatedAt(new DateTimeImmutable())
            ->setMessageRoute($event->getRoute())
            ->setMessageType((new ReflectionClass($event))->getShortName())
            ->setAggregateId($event->getAggregateId())
            ->setAggregateType($event->getAggregateType())
            ->setPayloadType($event->getPayloadType())
            ->setPayload($this->getPayload())
            ->setSchemaVersion(1);
    }

    private function getPayload() : string
    {
        return '';
    }
}
