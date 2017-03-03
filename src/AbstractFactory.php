<?php

namespace CQRSFactory;

use Interop\Container\ContainerInterface;

abstract class AbstractFactory
{
    /**
     * @var string
     */
    private $configKey;

    /**
     * @param string $configKey
     */
    public function __construct(string $configKey = 'cqrs_default')
    {
        $this->configKey = $configKey;
    }

    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function __invoke(ContainerInterface $container)
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
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws Exception\DomainException
     */
    public static function __callStatic($name, array $arguments)
    {
        if (!array_key_exists(0, $arguments) || $arguments[0] instanceof ContainerInterface) {
            throw new Exception\DomainException(sprintf(
                'The first argument must be of type %s',
                ContainerInterface::class
            ));
        }

        return (new static($name))->__invoke($arguments[0]);
    }

    /**
     * Creates a new instance from a specified config.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @return mixed
     */
    abstract protected function createWithConfig(ContainerInterface $container, string $configKey);

    /**
     * Returns the default config.
     *
     * @return array
     */
    abstract protected function getDefaultConfig(): array;

    /**
     * Retrieves the config for a specific section.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @param string $section
     * @return array
     */
    protected function retrieveConfig(ContainerInterface $container, string $configKey, string $section): array
    {
        $applicationConfig = $container->has('config') ? $container->get('config') : [];
        $config = $applicationConfig['cqrs'][$section][$configKey] ?? [];

        return array_merge(
            $this->getDefaultConfig(),
            $config
        );
    }

    /**
     * Retrieves a dependency through the container.
     *
     * If the container does not know about the dependency, it is pulled from a fresh factory. This saves the user from
     * registering factories which they are not gonna access themself at all, and thus minimized configuration.
     *
     * @param ContainerInterface $container
     * @param string $configKey
     * @param string $section
     * @param string $factoryClassName
     * @return mixed
     */
    protected function retrieveDependency(
        ContainerInterface $container,
        string $configKey,
        string $section,
        string $factoryClassName
    ) {
        $containerKey = sprintf('cqrs.%s.%s', $section, $configKey);

        if ($container->has($containerKey)) {
            return $container->get($containerKey);
        }

        return (new $factoryClassName($configKey))->__invoke($container);
    }
}
