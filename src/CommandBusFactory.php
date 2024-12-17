<?php

namespace CQRSFactory;

use CQRS\CommandHandling\CommandBusInterface;
use CQRS\CommandHandling\PsrContainerCommandHandlerLocator;
use CQRS\CommandHandling\SequentialCommandBus;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\EventHandling\Publisher\EventPublisherInterface;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type CommandBusConfig array{
 *     class: class-string<CommandBusInterface>,
 *     handlers: array<class-string, string>,
 *     transaction_manager: string,
 *     event_publisher: string
 * }
 * @phpstan-extends AbstractFactory<CommandBusInterface>
 */
class CommandBusFactory extends AbstractFactory
{
    #[\Override]
    protected function createWithConfig(ContainerInterface $container, string $configKey): CommandBusInterface
    {
        /** @var CommandBusConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'command_bus');

        if ($config['class'] === SequentialCommandBus::class) {
            return new SequentialCommandBus(
                new PsrContainerCommandHandlerLocator(
                    $container,
                    $config['handlers']
                ),
                $this->retrieveDependency(
                    $container,
                    $config['transaction_manager'],
                    'transaction_manager',
                    TransactionManagerFactory::class
                ),
                $this->retrieveDependency(
                    $container,
                    $config['event_publisher'],
                    'event_publisher',
                    EventPublisherFactory::class
                )
            );
        }

        return new $config['class'];
    }

    /**
     * @phpstan-return CommandBusConfig
     */
    #[\Override]
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SequentialCommandBus::class,
            'handlers' => [],
            'transaction_manager' => 'cqrs_default',
            'event_publisher' => 'cqrs_default',
        ];
    }
}
