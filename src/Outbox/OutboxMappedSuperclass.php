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
     */
    protected UuidInterface $id;

    /** @ORM\Column(type="string") */
    protected string $messageKey;

    /** @ORM\Column(type="string") */
    protected string $messageRoute;

    /** @ORM\Column(type="string") */
    protected string $messageType;

    /** @ORM\Column(type="uuid") */
    protected UuidInterface $aggregateId;

    /** @ORM\Column(type="string") */
    protected string $aggregateType;

    /** @ORM\Column(type="string") */
    protected string $payloadType;

    /**
     * @ORM\Column(type="json_array", options={"jsonb"=true})
     *
     * @var mixed[]
     */
    protected array $payload;

    /** @ORM\Column(type="integer") */
    protected int $schemaVersion;

    /** @ORM\Column(type="utc_datetime_immutable") */
    protected DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="uuid", nullable=true)
     * @ORM\OneToOne(targetEntity=OutboxMappedSuperclass::class)
     */
    protected ?UuidInterface $previousEvent = null;

    public function fromOutboxEntry(
        OutboxEntry $outboxEntry,
        ?OutboxMappedSuperclass $previousEntity = null
    ): OutboxMappedSuperclass {
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

        if ($previousEntity instanceof OutboxMappedSuperclass) {
            $outbox->previousEvent = $previousEntity->getId();
        }

        return $outbox;
    }

    private function getId(): UuidInterface
    {
        return $this->id;
    }
}
