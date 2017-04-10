<?php

namespace CQRSFactory;

use CQRS\Serializer\HybridSerializer;
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
    protected function createWithConfig(ContainerInterface $container, string $configKey): SerializerInterface
    {
        $config = $this->retrieveConfig($container, $configKey, 'serializer');

        if ($config['class'] === HybridSerializer::class) {

            $dictionary = $config['event_type_map'] ?: [];

            /** @var JsonSerializer $jsonSerializer */
            $jsonSerializer = new JsonSerializer();

            return new $config['class'](
                $jsonSerializer,
                $dictionary

            );
        } else {

            return new $config['class'](
                is_string($config['instance'])
                    ? $container->get($config['instance'])
                    : $config['instance']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => JsonSerializer::class,
            'instance' => null,
        ];
    }
}
