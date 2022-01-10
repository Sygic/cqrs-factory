<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\DomainEventQueue;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use CQRS\EventHandling\Publisher\SimpleEventPublisher;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type EventPublisherConfig array{
 *     class: class-string<EventPublisherInterface>,
 *     event_bus: string,
 *     identity_map: string,
 *     event_store: string,
 *     entity_manager: string
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
            $entityManager = $this->retrieveService(
                $container,
                $config,
                'entity_manager',
                EntityManagerInterface::class
            );
            $entityManager->getEventManager()
                ->addEventSubscriber($eventPublisher);
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
            'entity_manager' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
