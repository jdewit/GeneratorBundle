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