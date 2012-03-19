<?php

namespace {{ bundle_namespace }}\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('{{ bundle_alias }}');
       
        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->defaultValue('{{ db_driver }}')->cannotBeEmpty()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }

    private function add{{ entity }}Section(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('{{ entity_cc }}')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('{{ entity_cc }}_class')->defaultValue('{{ bundle_namespace }}\Entity\{{ entity }}')->end()
                        ->scalarNode('{{ entity_cc }}_manager')->defaultValue('{{ bundle_alias }}.{{ entity_cc }}_manager'.default')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('{{ bundle_alias }}_{{ entity_cc }}')->end()
                                ->scalarNode('handler')->defaultValue('{{ bundle_alias }}.{{ entity_cc }}.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('{{ bundle_alias }}_{{ entity_cc }}_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
