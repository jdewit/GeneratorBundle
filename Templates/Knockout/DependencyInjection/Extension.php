<?php
namespace {{ bundleNamespace }}\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;

class {{ bundleVendor }}{{ bundleCoreName | ucFirst }}Extension extends Extension 
{
    public function load(array $configs, ContainerBuilder $container) {
        
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.yml');
    }
}

