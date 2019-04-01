<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\OutboxEvents;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Ramsey\Uuid\UuidInterface;

/**
 * @MappedSuperclass
 */
abstract class OutboxMappedSuperclass
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", name="id")
     *
     * @var UuidInterface
     */
    protected $id;

    /**
     * @ORM\Column(type="string", name="message_key", nullable=false)
     *
     * @var string
     */
    protected $messageKey;

    /**
     * @ORM\Column(type="string", name="message_route", nullable=false)
     *
     * @var string
     */
    protected $messageRoute;

    /**
     * @ORM\Column(type="string", name="message_type", nullable=false)
     *
     * @var string
     */
    protected $messageType;

    /**
     * @ORM\Column(type="uuid", name="aggregate_id", nullable=false)
     *
     * @var UuidInterface
     */
    protected $aggregateId;

    /**
     * @ORM\Column(type="string", name="aggregate_type", nullable=false)
     *
     * @var string
     */
    protected $aggregateType;

    /**
     * @ORM\Column(type="string", name="payload_type", nullable=false)
     *
     * @var string
     */
    protected $payloadType;

    /**
     * @ORM\Column(type="json", name="payload", nullable=false)
     *
     * @var string
     */
    protected $payload;

    /**
     * @ORM\Column(type="integer", name="schema_version", nullable=false)
     *
     * @var int
     */
    protected $schemaVersion;

    /**
     * @ORM\Column(type="utc_datetime_immutable", name="created_at", nullable=false)
     *
     * @var DateTimeImmutable
     */
    protected $createdAt;

    public function setId(UuidInterface $id) : self
    {
        $this->id = $id;

        return $this;
    }

    public function setMessageKey(string $messageKey) : self
    {
        $this->messageKey = $messageKey;

        return $this;
    }

    public function setMessageRoute(string $messageRoute) : self
    {
        $this->messageRoute = $messageRoute;

        return $this;
    }

    public function setMessageType(string $messageType) : self
    {
        $this->messageType = $messageType;

        return $this;
    }

    public function setAggregateId(UuidInterface $aggregateId) : self
    {
        $this->aggregateId = $aggregateId;

        return $this;
    }

    public function setAggregateType(string $aggregateType) : self
    {
        $this->aggregateType = $aggregateType;

        return $this;
    }

    public function setPayloadType(string $payloadType) : self
    {
        $this->payloadType = $payloadType;

        return $this;
    }

    public function setPayload(string $payload) : self
    {
        $this->payload = $payload;

        return $this;
    }

    public function setSchemaVersion(int $schemaVersion) : self
    {
        $this->schemaVersion = $schemaVersion;

        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt) : self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
