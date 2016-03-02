<?php

namespace CQRSFactory;

use CQRS\Serializer\JsonSerializer;
use CQRS\Serializer\SerializerInterface;
use Interop\Container\ContainerInterface;

class SerializerFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $configKey
     * @return SerializerInterface
     */
    protected function createWithConfig(ContainerInterface $container, $configKey)
    {
        $config = $this->retrieveConfig($container, $configKey, 'serializer');

        return new $config['class'](
            is_string($config['instance'])
                ? $container->get($config['instance'])
                : $config['instance']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return [
            'class' => JsonSerializer::class,
            'instance' => null,
        ];
    }
}
