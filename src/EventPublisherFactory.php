<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\DomainEventQueue;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use Psr\Container\ContainerInterface;

class EventPublisherFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, string $configKey): EventPublisherInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'event_publisher');

        $eventPublisher = new $config['class'](
            $this->retrieveDependency(
                $container,
                $config['event_bus'],
                'event_bus',
                EventBusFactory::class
            ),
            new DomainEventQueue(
                $this->retrieveDependency(
                    $container,
                    $config['identity_map'],
                    'identity_map',
                    IdentityMapFactory::class
                )
            ),
            $this->retrieveDependency(
                $container,
                $config['event_store'],
                'event_store',
                EventStoreFactory::class
            )
        );

        return $eventPublisher;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SimpleEventPublisher::class,
            'event_bus' => 'cqrs_default',
            'identity_map' => 'cqrs_default',
            'event_store' => 'cqrs_default',
        ];
    }
}
