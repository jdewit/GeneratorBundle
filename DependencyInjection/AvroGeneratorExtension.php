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
        $container->setParameter('avro_generator.add_fields', $config['add_fields']);
        $container->setParameter('avro_generator.use_owner', $config['use_owner']);

        $supportedStyles = array('avro');

        if (in_array($config['style'], $supportedStyles)) {
            $loader->load(sprintf('templates/%s.yml', $config['style']));
        }

        if ($container->hasParameter('avro_generator.files')) {
            $container->setParameter('avro_generator.files', array_merge($container->getParameter('avro_generator.files'), $config['files'])); 
        } else {
            $container->setParameter('avro_generator.files', $config['files']); 
        }
        if ($container->hasParameter('avro_generator.standalone_files')) {
            $container->setParameter('avro_generator.standalone_files', array_merge($container->getParameter('avro_generator.standalone_files'), $config['standalone_files'])); 
        } else {
            $container->setParameter('avro_generator.standalone_files', $config['standalone_files']); 
        }
        if ($container->hasParameter('avro_generator.bundle_folders')) {
            $container->setParameter('avro_generator.bundle_folders', array_merge($container->getParameter('avro_generator.bundle_folders'), $config['bundle_folders'])); 
        } else {
            $container->setParameter('avro_generator.bundle_folders', $config['bundle_folders']); 
        }
        if ($container->hasParameter('avro_generator.bundle_files')) {
            $container->setParameter('avro_generator.bundle_files', array_merge($container->getParameter('avro_generator.bundle_files'), $config['bundle_files'])); 
        } else {
            $container->setParameter('avro_generator.bundle_files', $config['bundle_files']); 
        }
    }
}
