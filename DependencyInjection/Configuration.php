<?php

namespace Avro\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
* Contains the configuration information for the bundle
*
* @author Joris de Wit <joris.w.dewit@gmail.com>
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
        $rootNode = $treeBuilder->root('avro_generator');

        $supportedStyles = array('Avro', 'none');

        $rootNode
            ->children()
                ->scalarNode('style')
                    ->validate()
                        ->ifNotInArray($supportedStyles)
                        ->thenInvalid('The style %s is not supported. Please choose one of '.json_encode($supportedStyles))
                    ->end()
                    ->defaultValue('Avro')
                ->end()
                ->booleanNode('overwrite')->defaultFalse()->end()
             ->end();


        return $treeBuilder;
    }

}
