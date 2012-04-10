<?php
namespace Avro\GeneratorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Alias;

class AvroGeneratorExtension extends Extension 
{
    public function load(array $configs, ContainerBuilder $container) 
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('config.yml');
        $loader->load('services/manipulators/routing.yml');
        $loader->load('services/manipulators/config.yml');

        $container->setParameter('avro_generator.style', $config['style']);
        $container->setParameter('avro_generator.overwrite', $config['overwrite']);

        switch ($config['style']) {
            case 'Avro':
                $loader->load('avro.yml');
            break;
            case 'Fos':
                $loader->load('fos.yml');
            break;
        }
    }
}
