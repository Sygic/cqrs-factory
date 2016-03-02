<?php

namespace CQRSFactory;

use CQRS\CommandHandling\TransactionManager\NoTransactionManager;
use CQRSTest\Plugin\Doctrine\CommandHandling\ExplicitOrmTransactionManagerTest;
use CQRSTest\Plugin\Doctrine\CommandHandling\ImplicitOrmTransactionManagerTest;
use Interop\Container\ContainerInterface;

class TransactionManagerFactory extends AbstractFactory
{
    /**
     * {@inheritdoc}
     */
    public function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'transaction_manager');

        switch ($config['class']) {
            case ExplicitOrmTransactionManagerTest::class:
            case ImplicitOrmTransactionManagerTest::class:
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
    protected function getDefaultConfig()
    {
        return [
            'class' => NoTransactionManager::class,
            'connection' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
