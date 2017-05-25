<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\IdentityMapInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Psr\Container\ContainerInterface;

class IdentityMapFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return IdentityMapInterface
     */
    protected function createWithConfig(ContainerInterface $container, string $configKey): IdentityMapInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'identity_map');

        return new $config['class'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SimpleIdentityMap::class,
        ];
    }
}
