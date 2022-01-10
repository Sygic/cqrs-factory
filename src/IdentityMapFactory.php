<?php

namespace CQRSFactory;

use CQRS\EventHandling\Publisher\IdentityMapInterface;
use CQRS\EventHandling\Publisher\SimpleIdentityMap;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-type IdentityMapConfig array{class: class-string<IdentityMapInterface>, entity_manager?: string}
 * @phpstan-extends AbstractFactory<IdentityMapInterface>
 */
class IdentityMapFactory extends AbstractFactory
{
    protected function createWithConfig(ContainerInterface $container, string $configKey): IdentityMapInterface
    {
        /** @var IdentityMapConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'identity_map');

        $identityMap = new $config['class'];

        if ($identityMap instanceof EventSubscriber) {
            $entityManager = $this->retrieveService(
                $container,
                $config,
                'entity_manager',
                EntityManagerInterface::class
            );

            $entityManager->getEventManager()
                ->addEventSubscriber($identityMap);
        }

        return $identityMap;
    }

    /**
     * @return IdentityMapConfig
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SimpleIdentityMap::class,
            'entity_manager' => 'doctrine.entity_manager.orm_default',
        ];
    }
}
