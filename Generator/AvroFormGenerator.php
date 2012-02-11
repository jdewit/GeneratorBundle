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

/**
 * Generates a formType and FormHandler based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroFormGenerator extends Generator
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
        
        $parts = explode('\\', $entity);
        
        $parameters = array(
            'entity' => $this->entity,
            'entity_lc' => $this->entityLC,
            'fields' => $this->fields,
            'bundle_name' => $this->bundleName,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,  
            'bundle_vendor' => $this->bundleVendor,
            'bundle_basename' => $this->bundleBasename,
            'bundle_alias' => $this->bundleAlias,  
            'bundle_alias_cc' => $this->bundleAliasCC,
            'db_driver' => $this->dbDriver,
            'style' => $this->style        
        );

        $this->output->write('Generating '.$this->bundleName.'/Form/'.$this->entity.'FormType.php: ');
        try {
            $this->generateFormType($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }       
        
        $this->output->write('Generating '.$this->bundleName.'/Form/'.$this->entity.'.FormHandler.php: ');
        try {
            $this->generateFormHandler($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }        
}
    
    /**
     * Generates the FormType in the final bundle.
     *
     * @param array $parameters needed to generate file 
     */
    private function generateFormType($parameters)
    {   
        $filename = $this->bundlePath.'/Form/Type/'.$this->entity.'FormType.php';      
        
        $this->renderFile('Form/Type/FormType.php', $filename, $parameters);
    }    
    
    /**
     * Generates the FormHandler in the final bundle.
     *
     * @param array $parameters needed to generate file 
     */
    private function generateFormHandler($parameters)
    {
        $filename = $this->bundlePath.'/Form/Handler/'.$this->entity.'FormHandler.php';             
        
        $this->renderFile('Form/Handler/FormHandler.php', $filename, $parameters);       
    }       
    
}
