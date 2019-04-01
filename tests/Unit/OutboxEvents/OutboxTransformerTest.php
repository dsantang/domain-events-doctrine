<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit;

use DateTimeImmutable;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxTransformer;
use Dsantang\DomainEventsDoctrine\Tests\OutboxSubClass;
use Dsantang\DomainEventsDoctrine\Tests\RandomOutboxEvent;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class OutboxTransformerTest extends TestCase
{
    /** @var OutboxTransformer */
    private $transformer;

    /**
     * @before
     */
    public function setUpDependencies() : void
    {
        $this->transformer = new OutboxTransformer(new OutboxSubClass());
    }

    public function testTransform() : void
    {
        $transformationResult = $this->transformer->transform(new RandomOutboxEvent());

        $this->assertEquals($transformationResult, $this->getExpectedTransformationEntity());
    }

    private function getExpectedTransformationEntity()
    {
        $entity = new OutboxSubClass();

        return $entity
            ->setId(Uuid::fromString('4b4b9f22-548b-11e9-8647-d663bd873d93'))
            ->setMessageRoute('aggregate.order')
            ->setMessageType('RandomOutboxEvent')
            ->setAggregateId(Uuid::fromString('d1702762-548b-11e9-8647-d663bd873d93'))
            ->setAggregateType('Order')
            ->setPayloadType('OrderStructure')
            ->setPayload('')
            ->setSchemaVersion(1)
            ->setCreatedAt(new DateTimeImmutable('2019-01-01 00:00:00.831883'));
    }
}
