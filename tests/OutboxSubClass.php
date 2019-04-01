<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests;

use Doctrine\ORM\Mapping as ORM;
use Dsantang\DomainEventsDoctrine\OutboxEvents\OutboxMappedSuperclass;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class OutboxSubClass extends OutboxMappedSuperclass
{
    /**
     * @ORM\Column(type="string", name="field_1", nullable=true)
     *
     * @var string
     */
    private $field1;

    /**
     * @ORM\Column(type="string", name="field_2", nullable=true)
     *
     * @var string
     */
    private $field2;
}
