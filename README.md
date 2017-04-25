# CQRS factories

[![Latest Stable Version](https://poser.pugx.org/pauci/cqrs-factory/v/stable)](https://packagist.org/packages/pauci/cqrs-factory)
[![Total Downloads](https://poser.pugx.org/pauci/cqrs-factory/downloads)](https://packagist.org/packages/pauci/cqrs-factory)
[![Build Status](https://travis-ci.org/pauci/cqrs-factory.svg?branch=master)](https://travis-ci.org/pauci/cqrs-factory)
[![Coverage Status](https://coveralls.io/repos/pauci/cqrs-factory/badge.png?branch=master)](https://coveralls.io/r/pauci/cqrs-factory)

[CQRS](https://github.com/pauci/cqrs) factories for PSR-11

This package provides a set of factories to be used with containers using the PSR-11 standard for an easy
CQRS integration in a project.

## Installation

The easiest way to install this package is through composer:

```bash
$ composer require pauci/cqrs-factory
```

## Configuration

In the general case where you are only using a single command bus, it's enough to define the command bus factory:

```php
return [
    'dependencies' => [
        'factories' => [
            'cqrs.command_bus.cqrs_default' => \CQRSFactory\CommandBusFactory::class,
        ],
    ],
];
```

If you want to add a second command bus, or use another name than "cqrs_default", you can do so by using the static
variants of the factories:

```php
return [
    'dependencies' => [
        'factories' => [
            'cqrs.command_bus.cqrs_other' => [\CQRSFactory\CommandBusFactory::class, 'cqrs_other'],
        ],
    ],
];
```

Each factory supplied by this package will by default look for a registered factory in the container. If it cannot find
one, it will automatically pull its dependencies from on-the-fly created factories. This saves you the hassle of
registering factories in your container which you may not need at all. Of course, you can always register those
factories when required. The following additional factories are available:

- ```\CQRSFactory\EventBusFactory``` (cqrs.event_bus.*)
- ```\CQRSFactory\EventPublisherFactory``` (cqrs.event_publisher.*)
- ```\CQRSFactory\EventStoreFactory``` (cqrs.event_store.*)
- ```\CQRSFactory\IdentityMapFactory``` (cqrs.identity_map.*)
- ```\CQRSFactory\SerializerFactory``` (cqrs.serializer.*)
- ```\CQRSFactory\TransactionManagerFactory``` (cqrs.transaction_manager.*)

Each of those factories supports the same static behavior as the command bus factory. For container specific
configurations, there are a few examples provided in the example directory:
                                                                                      
- [Zend\ServiceManager](example/zend-servicemanager.php)

## Example configuration

A complete example configuration can be found in [example/config.php](example/config.php).