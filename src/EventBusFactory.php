<?php

namespace CQRSFactory;

use CQRS\EventHandling\EventBusInterface;
use CQRS\EventHandling\EventHandlerLocator;
use CQRS\EventHandling\Exception\InvalidArgumentException;
use CQRS\EventHandling\SynchronousEventBus;
use CQRS\HandlerResolver\ContainerHandlerResolver;
use CQRS\HandlerResolver\EventHandlerResolver;
use Psr\Container\ContainerInterface;

class EventBusFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return EventBusInterface
     * @throws InvalidArgumentException
     */
    protected function createWithConfig(ContainerInterface $container, string $configKey): EventBusInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'event_bus');

        return new $config['class'](
            new EventHandlerLocator(
                $config['handlers'],
                new ContainerHandlerResolver(
                    $container,
                    new EventHandlerResolver()
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SynchronousEventBus::class,
            'handlers' => [],
        ];
    }
}
