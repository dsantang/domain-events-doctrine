<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests;

use Doctrine\ORM\Mapping as ORM;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxMappedSuperclass;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class OutboxSubClass extends OutboxMappedSuperclass
{
    /** @ORM\Column(type="string", name="field_1", nullable=true) */
    private string $field1;

    /** @ORM\Column(type="string", name="field_2", nullable=true) */
    private string $field2;

    public function getField1(): string
    {
        return $this->field1;
    }

    public function getField2(): string
    {
        return $this->field2;
    }
}
