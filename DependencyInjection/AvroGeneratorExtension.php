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
        $loader->load('services/manipulator.yml');

        $container->setParameter('avro_generator.style', $config['style']);
        $container->setParameter('avro_generator.overwrite', $config['overwrite']);

        switch ($config['style']) {
            case 'Avro':
                $loader->load('avro.yml');
                $container->setParameter('avro_generator.files', array_merge($container->getParameter('avro_generator.avro_files'), $container->getParameter('avro_generator.my_files')));
            break;
            case 'Fos':
                $loader->load('fos.yml');
                $container->setParameter('avro_generator.files', array_merge($container->getParameter('avro_generator.fos_files'), $container->getParameter('avro_generator.my_files')));
            break;
            case 'none':
                $container->setParameter('avro_generator.files', $container->getParameter('avro_generator.my_files'));
            break;
        }
    }
}
