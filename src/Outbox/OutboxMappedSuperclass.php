<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Outbox;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @MappedSuperclass
 */
abstract class OutboxMappedSuperclass
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $messageKey;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $messageRoute;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $messageType;

    /**
     * @ORM\Column(type="uuid")
     *
     * @var UuidInterface
     */
    protected $aggregateId;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $aggregateType;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    protected $payloadType;

    /**
     * @ORM\Column(type="json")
     *
     * @var string
     */
    protected $payload;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $schemaVersion;

    /**
     * @ORM\Column(type="utc_datetime_immutable")
     *
     * @var DateTimeImmutable
     */
    protected $createdAt;

    public function fromOutboxEntry(OutboxEntry $outboxEntry) : OutboxMappedSuperclass
    {
        $outbox = clone $this;

        $outbox->id            = Uuid::uuid4();
        $outbox->messageKey    = $outboxEntry->getMessageKey();
        $outbox->messageRoute  = $outboxEntry->getMessageRoute();
        $outbox->messageType   = $outboxEntry->getMessageType();
        $outbox->aggregateId   = $outboxEntry->getAggregateId();
        $outbox->aggregateType = $outboxEntry->getAggregateType();
        $outbox->payloadType   = $outboxEntry->getPayloadType();
        $outbox->payload       = $outboxEntry->getPayload();
        $outbox->schemaVersion = $outboxEntry->getSchemaVersion();
        $outbox->createdAt     = new DateTimeImmutable();

        return $outbox;
    }
}
