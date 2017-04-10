<?php

namespace CQRSFactory;

use CQRS\EventStore\ChainingEventStore;
use CQRS\EventStore\EventStoreInterface;
use CQRS\EventStore\FilteringEventStore;
use CQRS\EventStore\MemoryEventStore;
use Interop\Container\ContainerInterface;

class EventStoreFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return EventStoreInterface
     */
    protected function createWithConfig(ContainerInterface $container, string $configKey): EventStoreInterface
    {
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

                $eventFilter = $container->get($config['event_filter']);
                return new FilteringEventStore($eventStore, $eventFilter);

            case MemoryEventStore::class:
                return new MemoryEventStore();
        }

        return new $config['class'](
            $this->retrieveDependency(
                $container,
                $config['serializer'],
                'serializer',
                SerializerFactory::class
            ),
            is_string($config['connection'])
                ? $container->get($config['connection'])
                : $config['connection'],
            $config['namespace'],
            $config['size']
        );
    }

    /**
     * {@inheritdoc}
     */
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
