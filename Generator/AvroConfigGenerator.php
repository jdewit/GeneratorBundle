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
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Avro\GeneratorBundle\Generator\Generator;
use Avro\GeneratorBundle\Manipulator\BundleRoutingManipulator;
use Symfony\Component\Yaml\Parser;
use Avro\GeneratorBundle\Yaml\Dumper;

/**
 * Generates the Resources/config files based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroConfigGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     * @param string $entity The entity relative class name
     * @param array $fields The entity fields
     */
    public function generate()
    {
        $this->output->write('Generating '.$this->bundleName.'/Resources/config/services/'.$this->entityCC.'.xml: ');
        try {
            $this->generateEntityServices();
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }   

        $this->output->write('Generating '.$this->bundleName.'/Resources/config/services/orm_'.$this->entityCC.'.xml: ');
        try {
            $this->generateEntityDBServices();
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  
        
        $this->output->write('Updating app/config.yml: ');
        try {
            $this->updateEntityConfig();
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        } 
        
//        $this->output->write('Generating '.$this->bundleName.'/Resources/config/routing/'.$this->entityCC.'.xml: ');
//        try {
//            $this->generateEntityRouting($this->parameters);;
//            $this->output->writeln('<info>Ok</info>');
//        } catch (\RuntimeException $e) {
//            $this->output->writeln(array(
//                '<error>Fail</error>',
//                $e->getMessage(),
//                ''
//            ));
//        }         
        
//        $this->output->write('Updating '.$this->bundleName.'/Resources/config/routing.yml: ');
//        try {
//            $this->UpdateBundleRouting();;
//            $this->output->writeln('<info>Ok</info>');
//        } catch (\RuntimeException $e) {
//            $this->output->writeln(array(
//                '<error>Fail</error>',
//                $e->getMessage(),
//                ''
//            ));
//        }    
    }
            
    /*
     * Generate entities mapping file
     * 
     */
    private function generateModelMapping()
    {
        switch ($this->dbDriver):
            case 'orm':  
                $filename = $this->bundlePath.'/Resources/config/doctrine/'.$this->entityCC.'.orm.xml';
                
                $this->renderFile('Resources/config/orm.xml', $filename);    
                
                break;
            case 'mongodb':
                //TODO:
                break;
            case 'couchdb':
                //TODO:
                break;
        endswitch;        
    }    
    
    /*
     * Generate Entity Services 
     * 
     */
    private function generateEntityServices()
    {
        $filename = $this->bundlePath.'/Resources/config/services/'.$this->entityCC.'.xml';   
        
        $this->renderFile('Resources/config/services/services.xml', $filename);    
    }

    /*
     * Generate Entity DB Services 
     */
    private function generateEntityDBServices()
    {
        $filename = $this->bundlePath.'/Resources/config/services/'.$this->dbDriver.'_'.$this->entityCC.'.xml';   
        
        $this->renderFile('Resources/config/services/orm_entity.xml', $filename);    
    }    
    
    /*
     * Update Entity Configuration Reference
     */
    private function updateEntityConfig()
    {
        $parser = new Parser();
        $dumper = new Dumper();
        
        $code = array($this->bundleAlias => array($this->entityCC => null));
        
        // get bundles config.yml and convert to php
        $configPath = $this->container->getParameter('kernel.root_dir').'/config/config.yml';
        
        $config = $parser->parse(file_get_contents($configPath));
        
        // don't add configuration twice
        if (!empty($config[$this->bundleAlias][$this->entityCC])) {
            return true;
        }        
        
        if (empty($config[$this->bundleAlias])) {
            unset($config[$this->bundleAlias]);
        }
        
        $updatedConfig = array_merge_recursive($config, $code);
                
        $updatedConfig = $dumper->dump($updatedConfig, 3);
        
        //file_put_contents($this->bundlePath.'/Resources/config/config_temp.yml', );
        file_put_contents($configPath, $updatedConfig);   
             
    }
    
    /**
     * Generates the routing configuration.
     *
     */
    private function generateEntityRouting()
    {
        $filename = $this->bundlePath.'/Resources/config/routing/'.$this->entityCC.'.xml'; 

        $this->renderFile('Resources/config/routing/routing.xml', $filename);  
    }      
    
    /*
     * Updates bundles routing file
     * 
     */
    protected function updateBundleRouting()
    {
        $file = $this->bundlePath.'/Resources/config/routing.yml';
        $appRoutingFormat = 'yml';
        $routingManipulator = new BundleRoutingManipulator($file, $appRoutingFormat, $this->bundleName, $this->entityCC);
        $routingManipulator->update();
    }           
}
