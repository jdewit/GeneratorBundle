    private function add{{ entity }}Section(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('{{ entity_cc }}')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('{{ entity_cc }}_class')->defaultValue('{{ bundle_namespace }}\Entity\{{ entity }}')->end()
                        ->scalarNode('{{ entity_us }}_manager')->defaultValue('{{ bundle_alias }}.{{ entity_us }}_manager'.default')->end()
                        ->arrayNode('form')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('{{ bundle_alias }}_{{ entity_us }}')->end()
                                ->scalarNode('handler')->defaultValue('{{ bundle_alias }}.{{ entity_us }}.form.handler.default')->end()
                                ->scalarNode('name')->defaultValue('{{ bundle_alias }}_{{ entity_us }}_form')->cannotBeEmpty()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }