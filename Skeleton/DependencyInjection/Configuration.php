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
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('{{ bundle_alias }}');
       
        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->defaultValue('{{ db_driver }}')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
