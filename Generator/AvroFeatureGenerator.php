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
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroFeatureGenerator extends Generator
{
    protected $entity;
    protected $entityLC;
    protected $fields;
    
    /**
     * Generates the features if it does not exist.
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
            'db_driver' => $this->dbDriver        
        );

        $this->output->write('Generating '.$this->bundleName.'/Features/'.$this->entityLC.'.feature: ');
        try {
            $this->generateFeatures($parameters);
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
     * Generates the Features.
     *
     * @param array $parameters needed to generate file 
     */
    private function generateFeatures($parameters)
    {   
        $filename = $this->bundlePath.'/Features/'.$this->entityLC.'.feature';      
        
        $this->renderFile('Features/entity.feature', $filename, $parameters);
    }    
    
}
