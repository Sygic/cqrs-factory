<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\DomainEventQueue;
use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use CQRS\Plugin\Doctrine\EventHandling\Publisher\DoctrineEventPublisher;
use Interop\Container\ContainerInterface;

class EventPublisherFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, $configKey)
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

        if ($eventPublisher instanceof DoctrineEventPublisher) {
            $container->get($config['eventy_manager'])
                ->getEventManager()
                ->addEventSubscriber($eventPublisher);
        }

        return $eventPublisher;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return [
            'class' => SimpleEventPublisher::class,
            'event_bus' => 'cqrs_default',
            'identity_map' => 'cqrs_default',
            'event_store' => 'cqrs_default',
            'entity_manager' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
