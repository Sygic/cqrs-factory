<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\IdentityMapInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
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

        $identityMap = new $config['class'];

        if ($identityMap instanceof EventSubscriber) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $container->get($config['entity_manager']);
            $entityManager->getEventManager()
                ->addEventSubscriber($identityMap);
        }

        return $identityMap;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SimpleIdentityMap::class,
            'entity_manager' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
