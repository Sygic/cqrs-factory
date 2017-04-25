<?php
use Zend\ServiceManager\ServiceManager;

// Standard config keys
$container = new ServiceManager([
    'factories' => [
        'cqrs.command_bus.cqrs_default' => \CQRSFactory\CommandBusFactory::class,
    ],
]);

// Custom config keys
$container = new ServiceManager([
    'factories' => [
        'cqrs.command_bus.cqrs_other' => [\CQRSFactory\CommandBusFactory::class, 'cqrs_other'],
    ],
]);
