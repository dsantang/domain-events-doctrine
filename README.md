# Domain Events Doctrine
[![CircleCI](https://circleci.com/gh/dsantang/domain-events-doctrine.svg?style=svg)](https://circleci.com/gh/dsantang/domain-events-doctrine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/build-status/master)
[![SymfonyInsight](https://insight.symfony.com/projects/1ad0edc3-0f98-47af-89e3-434628e1fc7a/small.svg)](https://insight.symfony.com/projects/1ad0edc3-0f98-47af-89e3-434628e1fc7a)


This package is meant to be used in conjunction with [dsantang/domain-events](https://github.com/dsantang/domain-events),  
which provides the basic building blocks needed in order to create your application's domain events.  
**This package provides an integration with Doctrine ORM**,  
in order to automatically dispatch the recorded domain events once the ORM's `flush()` operation is successful.  
Events are dispatched via a Symfony's `EventDispatcherInterface`.  

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require dsantang/domain-events-doctrine
```

## Configuration

In order to automagically dispatch your domain events, you first need to add two event listeners to your Doctrine's `EventManager`.
These Doctrine's listeners will be listening to Doctrine's lifecycle events, and, only once the transaction has successfully been made,
they'll dispatch your domain events.

```php

use Dsantang\DomainEventsDoctrine\EventsRecorder\DoctrineEventsRecorder;
use Dsantang\DomainEventsDoctrine\Dispatcher\DoctrineEventsDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Doctrine\Common\EventManager;

// ...

$evm = new EventManager();

$dispatcher = new EventDispatcher();

// You can register here your domain event listeners to your dispatcher...

$tracker = new EventsTracker();

// Make sure they share the same `EventsTracker` instance.
$evm->addEventListener([Events::onFlush], new DoctrineEventsRecorder($tracker));
$evm->addEventListener([Events::postFlush], new DoctrineEventsDispatcher($tracker, $dispatcher));

// Remember to correctly wire this EventManager to your ORM.
```

That's it! You're all set!
Now you can add as many Symfony's listeners as you need to your `$dispatcher`,  
and you'll be able to react to the domain events raised by your application.

## Outbox pattern

This library also provides support for the [Outbox pattern](https://microservices.io/patterns/data/application-events.html) implementation.  
The idea behind the implementation is to be able to add entities to an "ongoing" transaction by hooking into Doctrine's `onFlush` event, 
creating "outbox" entries based on the application's domain events, and safely store them using the same DB transaction.  

In order to enrich your Domain Event and be able to set all the data required by the Outbox entity, 
you need to create a `Converter` class (by implementing `Dsantang\DomainEventsDoctrine\Outbox\Converter` interface) 
An example of an outbox event is as follows:

```php
use Dsantang\DomainEvents\DomainEvent;
use Dsantang\DomainEventsDoctrine\Outbox\Converter;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxEntry;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class YourOutboxConverter implements Converter
{
    public function convert(DomainEvent $domainEvent) : OutboxEntry
    {
        return new YourOutboxEntry($domainEvent);
    }
}

final class YourOutboxEntry implements OutboxEntry
{
    /** @var DomainEvent */
    private $domainEvent;

    public function __construct(DomainEvent $domainEvent)
    {
        $this->domainEvent = $domainEvent;
    }
    
    public function getName() : string
    {
        return 'OrderDispatched';
    }

    public function getAggregateId() : UuidInterface
    {
        return Uuid::fromString('d1702762-548b-11e9-8647-d663bd873d93');
    }

    public function getAggregateType() : string
    {
        return 'Order';
    }

    public function getPayloadType() : string
    {
        return 'OrderStructure';
    }

    public function getMessageKey() : string
    {
        return 'd663bd873d93';
    }

    public function getMessageRoute() : string
    {
        return 'aggregate.order';
    }

    public function getMessageType() : string
    {
        return 'OrderCreated';
    }

    public function getPayload() : string
    {
        return json_encode($this->domainEvent)
    }

    public function getSchemaVersion() : int
    {
        return 1;
    }
}    
```
In order to persist your Outbox entries, you must create a Doctrine Entity class inside your application that extends 
`Dsantang\DomainEventsDoctrine\Outbox\OutboxMappedSuperclass`.  
Please note that this approach uses [Doctrine's Inheritance Mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html#mapped-superclasses):  

Here is an example of an Outbox Entity class:   
```php
namespace YourNamespace;

use Doctrine\ORM\Mapping as ORM;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxMappedSuperclass;

/**
 * @ORM\Entity()
 * @ORM\Table
 */
class YourOutboxEntity extends OutboxMappedSuperclass
{
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $someAdditionalField;
}
```

And an example of the required configuration as is follows:  
**Warning:** this solution assumes that you're using `Dsantang\DomainEvents\DomainEvent` in order to raise your domain events.

```php
use Dsantang\DomainEventsDoctrine\Outbox\MapBased;
use Dsantang\DomainEventsDoctrine\Outbox\OutboxMappedSuperclass;

// Your class must extend OutboxMappedSuperclass
$yourOutboxEntity = new YourOutboxEntity();

$mapBased = new MapBased($yourOutboxEntity);
$mapBased->addConverter('YouNamespace\YourDomainEvent', new YourOutboxConverter());

// Always use with OnFlush event
$evm->addEventListener([Events::onFlush], $mapBased);

```
