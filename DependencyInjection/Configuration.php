<?php

namespace Prokl\TwigExtensionsPackBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Prokl\TwigExtensionsPackBundle\DependencyInjection
 *
 * @since 10.05.2021
 *
 * @psalm-suppress PossiblyUndefinedMethod
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('twig_extension_pack');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('webpack_build_dev_path')->defaultValue('local/build/')->end()
                ->scalarNode('webpack_build_production_path')->defaultValue('local/dist/')->end()
                ->scalarNode('cacher')->defaultValue('cache.app')->end()
                ->scalarNode('runtimes_export')->defaultValue(false)->end()
            ->end();

        return $treeBuilder;
    }
}
