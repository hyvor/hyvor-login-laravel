<?php

namespace Hyvor\Internal\Bundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator;

class HyvorInternalBundle extends AbstractBundle
{

    protected string $extensionAlias = 'internal';

    public function configure(DefinitionConfigurator $definition): void
    {
        /**
         * component: string
         * instance: string
         * private_instance: string
         * fake: bool
         */
        $definition->rootNode() // @phpstan-ignore-line
        ->children()
            ->scalarNode('component')->defaultValue('core')->end()
            ->scalarNode('instance')->defaultValue('%env(HYVOR_INSTANCE)%')->end()
            ->scalarNode('private_instance')->defaultValue('%env(HYVOR_PRIVATE_INSTANCE)%')->end()
            ->booleanNode('fake')->defaultValue('%env(HYVOR_FAKE)%')->end()
            ->end();
    }

    /**
     * @param array<mixed> $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.php');

        // ENV DEFAULTS
        $container->parameters()->set('env(HYVOR_INSTANCE)', 'https://hyvor.com');
        $container->parameters()->set('env(HYVOR_PRIVATE_INSTANCE)', null);
        $container->parameters()->set('env(HYVOR_FAKE)', '0');

        // InternalConfig class
        $container->services()
            ->get(InternalConfig::class)
            ->args([
                '%env(APP_SECRET)%',
                $config['component'],
                $config['instance'],
                $config['private_instance'],
                $config['fake'],
            ]);
    }

}