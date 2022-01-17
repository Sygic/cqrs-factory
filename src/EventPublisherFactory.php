<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\DomainEventQueue;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type EventPublisherConfig array{
 *     class: class-string<EventPublisherInterface>,
 *     event_bus: string,
 *     identity_map: string,
 *     event_store: string,
 *     event_manager: string
 * }
 * @phpstan-extends AbstractFactory<EventPublisherInterface>
 */
class EventPublisherFactory extends AbstractFactory
{
    public function createWithConfig(ContainerInterface $container, string $configKey): EventPublisherInterface
    {
        /** @var EventPublisherConfig $config */
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

        if ($eventPublisher instanceof EventSubscriber) {
            $eventManager = $this->retrieveService(
                $container,
                $config,
                'event_manager',
                EventManager::class
            );
            $eventManager->addEventSubscriber($eventPublisher);
        }

        return $eventPublisher;
    }

    /**
     * @phpstan-return EventPublisherConfig
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SimpleEventPublisher::class,
            'event_bus' => 'cqrs_default',
            'identity_map' => 'cqrs_default',
            'event_store' => 'cqrs_default',
            'event_manager' => 'doctrine.event_manager.orm_default',
        ];
    }
}
