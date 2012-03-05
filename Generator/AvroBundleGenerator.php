<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Util\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Avro\GeneratorBundle\Generator\Generator;
use Avro\GeneratorBundle\Manipulator\RoutingManipulator;
use Avro\GeneratorBundle\Manipulator\KernelManipulator;

/**
 * Generates a bundle.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroBundleGenerator extends Generator
{        
    public function generate($thirdParty, $vendor, $basename, $bundleNamespace, $bundleName, $dir, $dbDriver, $updateConfig)
    {
        $this->thirdParty = $thirdParty;
        $this->filesystem = $this->container->get('filesystem');
        $this->bundleName = $bundleName;
        
        $this->parameters = array(
            'third_party' => $thirdParty,
            'bundle_vendor' => $vendor,
            'bundle_namespace' => $bundleNamespace,
            'bundle_name' => $bundleName,
            'bundle_basename' => $basename,
            'bundle_alias' => strtolower($vendor.'_'.str_replace('Bundle', '', $basename)),
            'bundle_alias_cc' => $vendor.str_replace('Bundle', '', $basename),
            'db_driver' => $dbDriver,
        );

        $this->output->write('Creating bundle structure: ');
        try {
            $this->createBundleStructure($dir);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }         
        
        if ($updateConfig){
            $this->output->write('Adding bundle to AppKernel.php: ');
            try {
                $this->updateAppKernel();
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }        

            $this->output->write('Updating app/config/routing.yml: ');
            try {
                $this->UpdateAppRouting();
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }            
        }
    }
    
    protected function createBundleStructure($dir)
    {
      
        //create bundle.php
        $this->renderFile('Bundle.php', $dir.'/'.$this->parameters['bundle_name'].'.php', $this->parameters);      
        $this->renderFile('Resources/views/layout.html.twig', $dir.'/Resources/views/layout.html.twig', $this->parameters);
        $this->renderFile('Resources/config/routing.yml', $dir.'/Resources/config/routing.yml', $this->parameters);
        $this->renderFile('Resources/config/config.yml', $dir.'/Resources/config/config.yml', $this->parameters);
        $this->renderFile('README.md', $dir.'/README.md', $this->parameters);
        $this->renderFile('Resources/meta/LICENSE', $dir.'/Resources/meta/LICENSE', $this->parameters);
        
        //$this->renderFile('DependencyInjection/Extension.php', $dir.'/DependencyInjection/'.$this->parameters['bundle_alias_cc'].'extension.php', $this->parameters);
        
        //generate file structure
        $this->filesystem->mkdir($dir.'/Controller');
        $this->filesystem->mkdir($dir.'/Form');
        $this->filesystem->mkdir($dir.'/Form/Type');
        $this->filesystem->mkdir($dir.'/Form/Handler');
        $this->filesystem->mkdir($dir.'/Resources/config/services');

        if ($this->thirdParty) {
            $this->filesystem->mkdir($dir.'/Model');
            $this->filesystem->mkdir($dir.'/Document');
            $this->filesystem->mkdir($dir.'/Entity');  
        } else {
            switch ($this->parameters['db_driver']):
                case 'orm':
                    $this->filesystem->mkdir($dir.'/Entity');  
                break;
                case 'mongodb':
                    $this->filesystem->mkdir($dir.'/Document');
                break;
            endswitch;
        }
        $this->filesystem->mkdir($dir.'/Resources/doc');
        $this->filesystem->mkdir($dir.'/Resources/translations');
        $this->filesystem->mkdir($dir.'/Resources/public/css');
        $this->filesystem->mkdir($dir.'/Resources/public/images');
        $this->filesystem->mkdir($dir.'/Resources/public/js');
        $this->filesystem->mkdir($dir.'/Resources/public/uploads/images');
        
    }
    
    protected function updateAppKernel()
    {
        $kernelManipulator = new KernelManipulator($this->container->get('kernel'));
        $kernelManipulator->addBundle($this->parameters['bundle_namespace'], $this->parameters['bundle_name']);
    }    
    
    protected function updateAppRouting()
    {
        $filename = $this->container->getParameter('kernel.root_dir').'/config/routing.yml';

        $format = 'yml';

        $routingManipulator = new RoutingManipulator($filename, $this->bundleName);
        $routingManipulator->updateAppRouting($format);
     
    }
}
