<?php

declare(strict_types=1);

namespace Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEvents\EventAware;
use Dsantang\DomainEvents\Registry\OrderedEventRegistry;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\FirstDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\SecondDomainEvent;
use Dsantang\DomainEventsDoctrine\Tests\Unit\Outbox\Stub\DomainEvents\ThirdDomainEvent;
use PHPUnit\Framework\MockObject\MockObject;

trait EventArgsProvider
{
    /** @var MockObject entityManager */
    private $entityManager;

    /** @var MockObject unitOfWork */
    private $unitOfWork;

    /**
     * @return OnFlushEventArgs|MockObject
     */
    private function getEventArgs(bool $withEvents = true) : OnFlushEventArgs
    {
        $insertions = $updates = $deletions = [];

        if ($withEvents) {
            $entity1 = new class() implements EventAware {
                use OrderedEventRegistry;

                public function trigger(DomainEvent $event) : void
                {
                    $this->triggeredA($event);
                }
            };

            $entity2 = new class() implements EventAware {
                use OrderedEventRegistry;

                public function trigger(DomainEvent $event) : void
                {
                    $this->triggeredA($event);
                }
            };

            $entity3 = new class() implements EventAware {
                use OrderedEventRegistry;

                public function trigger(DomainEvent $event) : void
                {
                    $this->triggeredA($event);
                }
            };

            $domainEvent1 = new FirstDomainEvent();
            $domainEvent2 = new SecondDomainEvent();
            $domainEvent3 = new ThirdDomainEvent();

            $entity1->trigger($domainEvent1);
            $entity2->trigger($domainEvent2);
            $entity3->trigger($domainEvent3);

            $updates   = [$entity3];
            $deletions = [$entity2, $entity1];
        }

        $this->unitOfWork->expects(self::any())->method('getScheduledEntityInsertions')->willReturn($insertions);
        $this->unitOfWork->expects(self::any())->method('getScheduledEntityUpdates')->willReturn($updates);
        $this->unitOfWork->expects(self::any())->method('getScheduledEntityDeletions')->willReturn($deletions);

        $this->entityManager->expects(self::any())->method('getUnitOfWork')->willReturn($this->unitOfWork);

        $eventArgs = $this->createMock(OnFlushEventArgs::class);
        $eventArgs->expects(self::any())->method('getEntityManager')->willReturn($this->entityManager);

        return $eventArgs;
    }
}
