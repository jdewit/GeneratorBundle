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
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroDependencyInjectionGenerator extends Generator
{
    /**
     * Generates the dependency injection classes.
     */
    public function generate()
    {
        $this->output->write('Updating '.$this->bundleBasename.'/DependancyInjection/Configuration.php: ');
        try {
            $this->updateConfiguration();
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
            $this->updateExtension();
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
     */
    protected function updateConfiguration()
    {
        $partial = $this->bundlePath.'/DependencyInjection/ConfigurationPartial.php';
     
        // generate partial                
        $this->renderFile('DependencyInjection/ConfigurationPartial.php', $partial); 
        
        // manipulate Configuration.php file
        $manip = new ConfigurationManipulator($this->bundlePath.'/DependencyInjection/Configuration.php');
        try {
            $manip->addPartial($partial, $this->entity);
            unlink($partial);
        } catch (\RuntimeException $e) {
            unlink($partial);
            throw new \RuntimeException($e->getMessage());
        }                
    }    
    
    
    /*
     * Update Extension.php
     */
    private function updateExtension()
    {
        $partial = $this->bundlePath.'/DependencyInjection/ExtensionPartial.php';
     
        // generate partial                
        $this->renderFile('DependencyInjection/ExtensionPartial.php', $partial); 
        
        // manipulate Extension.php file
        $manip = new ExtensionManipulator($this->bundlePath.'/DependencyInjection/'.$this->bundleAliasCC.'Extension.php');
        $manip->addPartial($partial, $this->entity);
        
        //delete partial
        unlink($partial);      
    }
    
    
}
