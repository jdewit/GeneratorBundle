<?php
namespace {{ bundle_namespace }}\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class {{ bundle_alias_cc }}Extension extends Extension 
{
    public function load(array $configs, ContainerBuilder $container) {
        
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.yml');
        $loader->load('services.yml');
    }

    if (!empty($config['{{ entity_cc }}'])) {
        $loader->load('services/{{ entity_cc }}.xml');
        $loader->load(sprintf('services/%s_{{ entity_cc }}.xml', $config['db_driver']));

        $container->setAlias('{{ bundle_alias }}.{{ entity_cc }}_manager', $config['{{ entity_cc }}']['{{ entity_cc }}_manager']);
        $container->setAlias('{{ bundle_alias }}.{{ entity_cc }}.form.handler', $config['{{ entity_cc }}']['form']['handler']);
        unset($config['{{ entity_cc }}']['form']['handler']);

        $this->remapParametersNamespaces($config['{{ entity_cc }}'], $container, array(
            '' => array(
                '{{ entity_cc }}_class' => '{{ bundle_alias }}.model.{{ entity_cc }}.class',
            ),
            'form' => '{{ bundle_alias }}.{{ entity_cc }}.form.%s',
        ));
    }    
}

