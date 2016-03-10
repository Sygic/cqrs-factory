<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\IdentityMapInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use CQRS\Plugin\Doctrine\EventHandling\Publisher\DoctrineIdentityMap;
use Interop\Container\ContainerInterface;

class IdentityMapFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return IdentityMapInterface
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'identity_map');

        if ($config['class'] === DoctrineIdentityMap::class) {
            return new DoctrineIdentityMap(
                $container->get($config['entity_manager'])
            );
        }

        return new $config['class'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return [
            'class' => SimpleIdentityMap::class,
            'entity_manager' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
