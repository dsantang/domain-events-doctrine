# Domain Events Doctrine
[![CircleCI](https://circleci.com/gh/dsantang/domain-events-doctrine.svg?style=svg)](https://circleci.com/gh/dsantang/domain-events-doctrine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/build-status/master)

**This package provides an integration with Doctrine ORM**,
Events are dispatched via a Symfony's `EventDispatcherInterface`.
This package is meant to be used in conjunction with [dsantang/domain-events](https://github.com/dsantang/domain-events),
which provides the basic building blocks needed in order to create your application's domain events,
in order to automatically dispatch the recorded Domain Events once the ORM's `flush` operation is successful.

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
