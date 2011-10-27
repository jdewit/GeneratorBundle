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
    protected $entity;
    protected $entityLC;
    protected $fields;
    
    /**
     * Generates the entity class if it does not exist.
     *
     * @param string $entity The entity relative class name
     * @param array $fields The entity fields
     */
    public function generate($entity, array $fields)
    {
        $this->entity = $entity;
        $this->entityLC = strtolower($entity);
        $this->fields = $fields;
        
        $parameters = array(
            'entity' => $this->entity,
            'entity_lc' => $this->entityLC,
            'entity_class' => $this->bundleNamespace.'\\Model\\'.$this->entity,
            'fields' => $this->fields,
            'bundle_name' => $this->bundleName,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,  
            'bundle_vendor' => $this->bundleVendor,
            'bundle_basename' => $this->bundleBasename,
            'bundle_alias' => $this->bundleAlias,          
            'db_driver' => $this->dbDriver,
            'actions' => array('show', 'list', 'new', 'edit', 'delete')
        );  
        
        $this->output->write('Generating '.$this->bundleName.'/Resources/config/services/'.$this->entityLC.'.xml: ');
        try {
            $this->generateEntityServices($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }   

        $this->output->write('Generating '.$this->bundleName.'/Resources/config/services/orm_'.$this->entityLC.'.xml: ');
        try {
            $this->generateEntityDBServices($parameters);
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
            $this->updateEntityConfig($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        } 
        
//        $this->output->write('Generating '.$this->bundleName.'/Resources/config/routing/'.$this->entityLC.'.xml: ');
//        try {
//            $this->generateEntityRouting($parameters);;
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
     * @param array $parameters needed to generate file
     */
    private function generateModelMapping($parameters)
    {
        switch ($parameters['db_driver']):
            case 'orm':  
                
                $filename = $this->bundlePath.'/Resources/config/doctrine/'.$this->entityLC.'.orm.xml';
                
                $this->renderFile('Resources/config/orm.xml', $filename, $parameters);    
                
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
     * @param array $parameters needed to generate file
     */
    private function generateEntityServices($parameters)
    {
        $filename = $this->bundlePath.'/Resources/config/services/'.$this->entityLC.'.xml';   
        
        $this->renderFile('Resources/config/services/services.xml', $filename, $parameters);    
    }

    /*
     * Generate Entity DB Services 
     * 
     * @param array $parameters needed to generate file
     */
    private function generateEntityDBServices($parameters)
    {
        $filename = $this->bundlePath.'/Resources/config/services/'.$this->dbDriver.'_'.$this->entityLC.'.xml';   
        
        $this->renderFile('Resources/config/services/orm_entity.xml', $filename, $parameters);    
    }    
    
    /*
     * Update Entity Configuration Reference
     * 
     * @param array $parameters needed to generate file
     */
    private function updateEntityConfig($parameters)
    {
        $parser = new Parser();
        $dumper = new Dumper();
        
        $code = array($this->bundleAlias => array($this->entityLC => null));
        
        // get bundles config.yml and convert to php
        $configPath = $this->container->getParameter('kernel.root_dir').'/config/config.yml';
        
        $config = $parser->parse(file_get_contents($configPath));
        
        // don't add configuration twice
        if (!empty($config[$this->bundleAlias][$this->entityLC])) {
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
    private function generateEntityRouting($parameters)
    {
        $filename = $this->bundlePath.'/Resources/config/routing/'.$this->entityLC.'.xml'; 

        $this->renderFile('Resources/config/routing/routing.xml', $filename, $parameters);  
    }      
    
    /*
     * Updates bundles routing file
     * 
     */
    protected function updateBundleRouting()
    {
        $file = $this->bundlePath.'/Resources/config/routing.yml';
        $appRoutingFormat = 'yml';
        $routingManipulator = new BundleRoutingManipulator($file, $appRoutingFormat, $this->bundleName, $this->entityLC);
        $routingManipulator->update();
    }           
}
