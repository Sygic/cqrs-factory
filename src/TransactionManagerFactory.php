<?php

namespace CQRSFactory;

use CQRS\CommandHandling\TransactionManager\NoTransactionManager;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManager;
use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type TransactionManagerConfig array{
 *     class: class-string<TransactionManagerInterface>,
 *     connection?: string
 * }
 * @phpstan-extends AbstractFactory<TransactionManagerInterface>
 */
class TransactionManagerFactory extends AbstractFactory
{
    public function createWithConfig(ContainerInterface $container, string $configKey): TransactionManagerInterface
    {
        /** @phpstan-var TransactionManagerConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'transaction_manager');

        switch ($config['class']) {
            case ExplicitOrmTransactionManager::class:
                $entityManager = $this->retrieveService(
                    $container,
                    $config,
                    'connection',
                    EntityManagerInterface::class
                );

                return new ExplicitOrmTransactionManager($entityManager);

            case ImplicitOrmTransactionManager::class:
                $entityManager = $this->retrieveService(
                    $container,
                    $config,
                    'connection',
                    EntityManagerInterface::class
                );

                return new ImplicitOrmTransactionManager($entityManager);
        }

        return new $config['class'];
    }

    /**
     * @phpstan-return TransactionManagerConfig
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => NoTransactionManager::class,
            'connection' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
