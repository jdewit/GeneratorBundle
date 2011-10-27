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
use Avro\GeneratorBundle\Manipulator\ConfigurationManipulator;
use Avro\GeneratorBundle\Manipulator\ExtensionManipulator;

/**
 * Generates the dependencyInjection files on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroDependencyInjectionGenerator extends Generator
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
            'bundle_basename' => $this->bundleBasename,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,  
            'bundle_alias' => $this->bundleAlias,   
            'bundle_alias_cc' => $this->bundleAliasCC,
            'db_driver' => $this->dbDriver
        );

        $this->output->write('Updating '.$this->bundleBasename.'/DependancyInjection/Configuration.php: ');
        try {
            $this->updateConfiguration($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }      
        
        $this->output->write('Updating '.$this->bundleBasename.'/DependencyInjection/'.$this->bundleAliasCC.'Extension.php: ');
        try {
            $this->updateExtension($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }     
        

    }
            
    /*
     * Update Configuration.php
     * 
     * @param array $parameters
     */
    protected function updateConfiguration($parameters)
    {
        $partial = $this->bundlePath.'/DependencyInjection/ConfigurationPartial.php';
     
        // generate partial                
        $this->renderFile('DependencyInjection/ConfigurationPartial.php', $partial, $parameters); 
        
        // manipulate Configuration.php file
        $manip = new ConfigurationManipulator($this->bundlePath.'/DependencyInjection/Configuration.php', $parameters);
        try {
            $manip->addPartial($partial, $parameters['entity']);
            unlink($partial);
        } catch (\RuntimeException $e) {
            unlink($partial);
            throw new \RuntimeException($e->getMessage());
        }                
    }    
    
    
    /*
     * Update Extension.php
     * 
     * @param array $parameters
     */
    private function updateExtension($parameters)
    {
        $partial = $this->bundlePath.'/DependencyInjection/ExtensionPartial.php';
     
        // generate partial                
        $this->renderFile('DependencyInjection/ExtensionPartial.php', $partial, $parameters); 
        
        // manipulate Extension.php file
        $manip = new ExtensionManipulator($this->bundlePath.'/DependencyInjection/'.$this->bundleAliasCC.'Extension.php', $parameters);
        $manip->addPartial($partial, $this->entity);
        
        //delete partial
        unlink($partial);      
    }
    
    
}
