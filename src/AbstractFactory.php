<?php

namespace CQRSFactory;

use CQRSFactory\Exception\DomainException;
use Psr\Container\ContainerInterface;

/**
 * @phpstan-template T of object
 * @internal
 */
abstract class AbstractFactory
{
    /** @internal */
    final public function __construct(
        private readonly string $configKey = 'cqrs_default'
    ) {
    }

    /**
     * @phpstan-return T
     */
    public function __invoke(ContainerInterface $container): object
    {
        return $this->createWithConfig($container, $this->configKey);
    }

    /**
     * Creates a new instance from a specified config, specifically meant to be used as static factory.
     *
     * In case you want to use another config key than "cqrs_default", you can add the following factory to your config:
     *
     * <code>
     * <?php
     * return [
     *     'cqrs.SECTION.cqrs_other' => [SpecificFactory::class, 'cqrs_other'],
     * ];
     * </code>
     *
     * @phpstan-param array<int, mixed> $arguments
     * @phpstan-return T
     * @throws Exception\DomainException
     */
    public static function __callStatic(string $name, array $arguments): object
    {
        // Disallow calling the static factory on the abstract base class itself.
        if (static::class === self::class) {
            throw new Exception\DomainException('Cannot invoke static factory on AbstractFactory directly.');
        }

        if (!array_key_exists(0, $arguments) || !$arguments[0] instanceof ContainerInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'The first argument must be of type %s',
                ContainerInterface::class
            ));
        }

        $factoryClass = static::class;

        return (new $factoryClass($name))($arguments[0]);
    }

    /**
     * Retrieves the config for a specific section.
     *
     * @phpstan-return T
     */
    abstract protected function createWithConfig(ContainerInterface $container, string $configKey): object;

    /**
     * Returns the default config.
     *
     * @phpstan-return array<string, mixed>
     */
    abstract protected function getDefaultConfig(): array;

    /**
     * Retrieves the config for a specific section.
     *
     * @phpstan-return array<string, mixed>
     */
    protected function retrieveConfig(ContainerInterface $container, string $configKey, string $section): array
    {
        /** @var array{cqrs?: array<string, array<string, mixed>>} $applicationConfig */
        $applicationConfig = $container->has('config') ? $container->get('config') : [];
        /** @var array<string, array<string, mixed>> $sectionConfig */
        $sectionConfig = $applicationConfig['cqrs'][$section] ?? [];

        if (array_key_exists($configKey, $sectionConfig)) {
            /** @var array<string, mixed> $overrides */
            $overrides = $sectionConfig[$configKey];

            return array_replace($this->getDefaultConfig(), $overrides);
        }

        return $this->getDefaultConfig();
    }

    /**
     * Retrieves a dependency through the container.
     *
     * If the container does not know about the dependency, it is pulled from a fresh factory. This saves the user from
     * registering factories which they are not gonna access themself at all, and thus minimized configuration.
     *
     * @phpstan-template Dependency of object
     * @phpstan-param class-string<AbstractFactory<Dependency>> $factoryClassName
     * @phpstan-return Dependency
     */
    protected function retrieveDependency(
        ContainerInterface $container,
        string $configKey,
        string $section,
        string $factoryClassName
    ): object {
        $containerKey = sprintf('cqrs.%s.%s', $section, $configKey);

        if ($container->has($containerKey)) {
            /** @phpstan-ignore-next-line */
            return $container->get($containerKey);
        }

        return (new $factoryClassName($configKey))($container);
    }

    /**
     * @phpstan-template Service of object
     * @phpstan-param array<string, mixed> $config
     * @phpstan-param class-string<Service> $className
     * @phpstan-return Service
     */
    protected function retrieveService(
        ContainerInterface $container,
        array $config,
        string $key,
        string $className
    ): object {
        $service = $config[$key] ?? null;

        if (is_string($service)) {
            $service = $container->get($service);
        }

        if (!$service instanceof $className) {
            throw new DomainException(sprintf(
                'Service "%s" must be an instance of %s, got %s',
                $key,
                $className,
                get_debug_type($service)
            ));
        }

        return $service;
    }
}
