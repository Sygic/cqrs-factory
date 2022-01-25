<?php

namespace CQRSFactory;

use CQRS\Serializer\SerializerInterface;
use CQRS\Serializer\SymfonySerializer;
use CQRSFactory\Exception\DomainException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

/**
 * @phpstan-type SerializerConfig array{
 *     class: class-string<SerializerInterface>,
 *     instance?: class-string|object,
 *     format?: string,
 *     context?: array
 * }
 * @phpstan-extends AbstractFactory<SerializerInterface>
 */
class SerializerFactory extends AbstractFactory
{
    protected function createWithConfig(ContainerInterface $container, string $configKey): SerializerInterface
    {
        /** @var SerializerConfig $config */
        $config = $this->retrieveConfig($container, $configKey, 'serializer');

        if ($config['class'] === SymfonySerializer::class) {
            $instance = $this->retrieveService(
                $container,
                $config,
                'instance',
                SymfonySerializerInterface::class
            );

            $format = $config['format'] ?? 'json';
            $context = $config['context'] ?? [];

            return new SymfonySerializer($instance, $format, $context);
        }

        return new $config['class'];
    }

    /**
     * @phpstan-return SerializerConfig
     */
    protected function getDefaultConfig(): array
    {
        return [
            'class' => SymfonySerializer::class,
            'instance' => SymfonySerializerInterface::class,
            'context' => [
                AbstractObjectNormalizer::PRESERVE_EMPTY_OBJECTS => true,
            ],
        ];
    }
}
