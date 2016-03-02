<?php

return [
    'cqrs' => [
        'command_bus' => [
            'cqrs_default' => [
                'class' => CQRS\CommandHandling\SequentialCommandBus::class,

                'handlers' => [
                    'CommandTypeA' => function ($command) {},
                    'CommandTypeB' => 'my_command_handler_alias',
                    'CommandTypeC' => ['my_command_handler_alias', 'methodName'],
                ],

                'transaction_manager' => 'cqrs_default',
                'event_publisher' => 'cqrs_default',
                'logger' => 'my_logger_alias',
            ],
        ],

        'event_publisher' => [
            'cqrs_default' => [
                'class' => CQRS\Plugin\Doctrine\EventHandling\Publisher\DoctrineEventPublisher::class,
                'event_bus' => 'cqrs_default',
                'identity_map' => 'cqrs_default',
                'event_store' => 'cqrs_default',
                'entity_manager' => 'doctrine.entity_manager.orm_default',
            ],
        ],

        'event_bus' => [
            'cqrs_default' => [
                'class' => CQRS\EventHandling\SynchronousEventBus::class,

                'handlers' => [
                    'EventTypeA' => function ($event, $metadata, $timestamp) {},
                    'EventTypeB' => 'my_event_handler_alias',
                    'EventTypeC' => ['my_event_handler_alias', 'methodName'],
                ],

                'logger' => 'my_logger_alias',
            ],
        ],

        'identity_map' => [
            'cqrs_default' => [
                'class' => CQRS\Plugin\Doctrine\EventHandling\Publisher\DoctrineIdentityMap::class,
                'entity_manager' => 'doctrine.entity_manager.orm_default',
            ],
        ],

        'event_store' => [
            'cqrs_default' => [
                'class' => CQRS\Plugin\Doctrine\EventStore\TableEventStore::class,
                'serializer' => 'cqrs_default',
            ],
        ],

        'serializer' => [
            'cqrs_default' => [
                'class' => CQRS\Serializer\JsonSerializer::class,
                //'instance' => 'my_serializer_alias',
            ],
        ],
    ],
];
