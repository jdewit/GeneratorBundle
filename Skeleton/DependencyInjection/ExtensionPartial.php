        
        if (!empty($config['{{ entity_lc }}'])) {
            $loader->load('services/{{ entity_lc }}.xml');
            $loader->load(sprintf('services/%s_{{ entity_lc }}.xml', $config['db_driver']));

            $container->setAlias('{{ bundle_alias }}.{{ entity_lc }}_manager', $config['{{ entity_lc }}']['{{ entity_lc }}_manager']);
            $container->setAlias('{{ bundle_alias }}.{{ entity_lc }}.form.handler', $config['{{ entity_lc }}']['form']['handler']);
            unset($config['{{ entity_lc }}']['form']['handler']);

            $this->remapParametersNamespaces($config['{{ entity_lc }}'], $container, array(
                '' => array(
                    '{{ entity_lc }}_class' => '{{ bundle_alias }}.model.{{ entity_lc }}.class',
                ),
                'form' => '{{ bundle_alias }}.{{ entity_lc }}.form.%s',
            ));
        }    