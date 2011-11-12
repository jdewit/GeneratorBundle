    private function add{{ entity }}Section(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('{{ entity_lc }}')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('{{ entity_lc }}_class')->defaultValue('{{ bundle_namespace }}\Entity\{{ entity }}')->end()
                        ->scalarNode('{{ entity_lc }}_manager')->defaultValue('{{ bundle_alias }}.{{ entity_lc }}_manager.default')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('{{ bundle_alias }}_{{ entity_lc }}')->end()
                                ->scalarNode('handler')->defaultValue('{{ bundle_alias }}.{{ entity_lc }}.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('{{ bundle_alias }}_{{ entity_lc }}_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }