# Domain Events Doctrine
[![CircleCI](https://circleci.com/gh/dsantang/domain-events-doctrine.svg?style=svg)](https://circleci.com/gh/dsantang/domain-events-doctrine)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/badges/build.png?b=master)](https://scrutinizer-ci.com/g/dsantang/domain-events-doctrine/build-status/master)

This package provides an integration with Doctrine ORM,
in order to automatically dispatch the recorded Domain Events once the ORM's `flush` operation is successful.

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```sh
php composer.phar require dsantang/domain-events-doctrine
```
