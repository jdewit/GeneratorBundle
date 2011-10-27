<?php

namespace Avro\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\KernelInterface;
use Avro\GeneratorBundle\Manipulator\KernelManipulator;
use Avro\GeneratorBundle\Manipulator\RoutingManipulator;
use Avro\GeneratorBundle\Manipulator\ConfigManipulator;

/**
 * Configures a 3rd party Avro Bundle with a Symfony2 application.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroAppConfigGenerator extends Generator
{
    public function generate($bundleNamespace, $bundleName, $vendor, $format, $appRoutingFormat)
    {         
        $this->output->writeln(array(
            'Configuring your application...',
        )); 
        
        $this->output->write('Adding bundle vendor to autoload.php: ');
        try {
            $this->checkAutoloader($bundleNamespace, $bundleName, $vendor);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }
        
        $this->output->write('Adding bundle to AppKernel.php: ');
        try {
            $this->updateKernel($bundleNamespace, $bundleName);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }        
        
        $this->output->write('Adding bundle to routing file: ');
        try {
            $this->updateRouting($bundleName, $appRoutingFormat);       
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }        
       
        $this->output->write('Including bundle config to app config file: ');
        try {
            $this->updateConfig($bundleName);       
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }              
    }

    protected function checkAutoloader($bundleNamespace, $bundleName, $vendor)
    {
        if (!class_exists($bundleNamespace.'\\'.$bundleName)) {
            throw new \RuntimeException(            'You need to register the bundle vendor in autoload.php.
            Edit the <comment>app/autoload.php</comment> file and register the bundle
            namespace at the top of the <comment>registerNamespaces()</comment> call:
            <comment>'.$vendor.'  => __DIR__/../vendor/bundles</comment> \n'
            );
        } else {
            return true;
        }   
    }

    protected function updateKernel($bundleNamespace, $bundleName)
    {
        $kernel = $this->container->get('kernel');
        $kernelManipulator = new KernelManipulator($kernel);
        $kernelManipulator->addBundle($bundleNamespace, $bundleName);
    }
    
    protected function updateRouting($bundleName, $appRoutingFormat)
    {
        $file = $this->container->getParameter('kernel.root_dir').'/config/routing.'.$appRoutingFormat;
        
        $routingManipulator = new RoutingManipulator($file, $appRoutingFormat, $bundleName);
        $routingManipulator->update();
    }       
    
    protected function updateConfig($bundleName)
    {
        $file = $this->container->getParameter('kernel.root_dir').'/config/config.yml';
        
        $configManipulator = new ConfigManipulator($file, $bundleName);
        $configManipulator->update();
    }
}
