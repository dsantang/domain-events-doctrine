<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\OutboxEvents;

use Doctrine\ORM\EntityManager;

class OutboxEntityPersistence
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function persist(OutboxMappedSuperclass $entity) : void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->getUnitOfWork()->computeChangeSets();
    }
}
