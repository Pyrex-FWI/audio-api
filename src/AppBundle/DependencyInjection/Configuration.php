<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder         = new TreeBuilder();
        $digital_dj_poolRoot = $treeBuilder->root('app');
        $digital_dj_poolRoot
            ->children()
                ->arrayNode('library')
                    ->children()
                        ->arrayNode('indexing_workflow')
                            ->children()
                                ->booleanNode('create_media_reference_before_read_tag')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
