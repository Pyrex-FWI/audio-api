<?php

namespace Pyrex\DupeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('pyrex_dupe');

        $rootNode->children()->scalarNode('dupe_dump_file')/*->isRequired()*/->end();

        $rootNode
        ->children()
            ->arrayNode('database')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('local_entity_manager_name')->defaultValue('pyrex_dupe')->end()
                    ->scalarNode('driver')->defaultValue('pdo_sqlite')->end()
                    ->scalarNode('path')->defaultValue('%pyrex_dump.dupe_databsee%')->end()
                ->end()
            ->end()
        ->end();
        $rootNode
            ->children()
                ->arrayNode('extensions')
                    ->treatNullLike([])
                    ->prototype('scalar')->end()
                    ->defaultValue(['mp3', 'mp4', 'flac'])
                ->end()
            ->end();

        return $treeBuilder;
    }
}
