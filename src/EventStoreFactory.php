<?php

namespace CQRSFactory;

use CQRS\EventStore\ChainingEventStore;
use CQRS\EventStore\EventFilterInterface;
use CQRS\EventStore\EventStoreInterface;
use CQRS\EventStore\FilteringEventStore;
use CQRS\EventStore\MemoryEventStore;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type EventStoreConfig array{
 *     class: class-string<EventStoreInterface>,
 *     event_store: string,
 *     event_stores: array<string>,
 *     event_filter: string,
 *     serializer: string,
 *     connection: class-string|object|null,
 *     namespace: ?string,
 *     size: ?int
 * }
 * @phpstan-extends AbstractFactory<EventStoreInterface>
 */
class EventStoreFactory extends AbstractFactory
{
    #[\Override]
    protected function createWithConfig(ContainerInterface $container, string $configKey): EventStoreInterface
    {
        /** @var EventStoreConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'event_store');

        switch ($config['class']) {
            case ChainingEventStore::class:
                $eventStores = [];
                foreach ($config['event_stores'] as $eventStore) {
                    $eventStores[] = $this->retrieveDependency(
                        $container,
                        $eventStore,
                        'event_store',
                        static::class
                    );
                }
                return new ChainingEventStore($eventStores);

            case FilteringEventStore::class:
                $eventStore = $this->retrieveDependency(
                    $container,
                    $config['event_store'],
                    'event_store',
                    static::class
                );

                $eventFilter = $this->retrieveService(
                    $container,
                    $config,
                    'event_filter',
                    EventFilterInterface::class
                );

                return new FilteringEventStore($eventStore, $eventFilter);

            case MemoryEventStore::class:
                return new MemoryEventStore();
        }

        $connection = $config['connection'];
        if (is_string($connection)) {
            $connection = $container->get($connection);
        }

        return new $config['class'](
            $this->retrieveDependency(
                $container,
                $config['serializer'],
                'serializer',
                SerializerFactory::class
            ),
            $connection,
            $config['namespace'],
            $config['size']
        );
    }

    /**
     * @return EventStoreConfig
     */
    #[\Override]
    protected function getDefaultConfig(): array
    {
        return [
            'class' => MemoryEventStore::class,
            'event_store' => '',
            'event_stores' => [],
            'event_filter' => 'cqrs_default',
            'serializer' => 'cqrs_default',
            'connection' => null,
            'namespace' => null,
            'size' => null,
        ];
    }
}
