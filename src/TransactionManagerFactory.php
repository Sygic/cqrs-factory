<?php

namespace CQRSFactory;

use CQRS\CommandHandling\TransactionManager\NoTransactionManager;
use CQRS\CommandHandling\TransactionManager\TransactionManagerInterface;
use CQRS\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManager;
use CQRS\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManager;
use Interop\Container\ContainerInterface;

class TransactionManagerFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, string $configKey): TransactionManagerInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'transaction_manager');

        switch ($config['class']) {
            case ExplicitOrmTransactionManager::class:
            case ImplicitOrmTransactionManager::class:
                $entityManager = is_string($config['connection'])
                    ? $container->get($config['connection'])
                    : $config['connection'];

                return new $config['class']($entityManager);
        }

        return new $config['class'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => NoTransactionManager::class,
            'connection' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
