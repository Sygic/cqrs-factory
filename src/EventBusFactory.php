<?php

namespace CQRSFactory;

use CQRS\EventHandling\EventBusInterface;
use CQRS\EventHandling\PsrContainerEventHandlerLocator;
use CQRS\EventHandling\SynchronousEventBus;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type EventBusConfig array{
 *     class: class-string<EventBusInterface>,
 *     handlers: array<class-string, array<string|array{handler: string, priority?: int}>>
 * }
 * @phpstan-extends AbstractFactory<EventBusInterface>
 */
class EventBusFactory extends AbstractFactory
{
    protected function createWithConfig(ContainerInterface $container, string $configKey): EventBusInterface
    {
        /** @var EventBusConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'event_bus');

        if ($config['class'] === SynchronousEventBus::class) {
            return new SynchronousEventBus(
                new PsrContainerEventHandlerLocator(
                    $container,
                    $config['handlers'],
                )
            );
        }

        return new $config['class'];
    }

    /**
     * @phpstan-return EventBusConfig
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SynchronousEventBus::class,
            'handlers' => [],
        ];
    }
}
