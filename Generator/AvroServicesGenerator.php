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
use Symfony\Component\Yaml\Parser;
use Avro\GeneratorBundle\Yaml\Dumper;

/**
 * Generates a services for an entity.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroServicesGenerator extends Generator
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
            'db_driver' => $this->dbDriver        
        );

       
        $this->output->write('Creating '.$this->bundleName.'/Resources/config/services/'.$this->entityLC.'.yml: ');
        try {
            $this->updateService($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  
}

    protected function createService($parameters, $targetFile)
    {
        $this->renderFile('Resources/config/services/servicesPartial.yml', $targetFile, $parameters);
    }

    protected function updateService($parameters)
    {
        $targetFile = $this->bundlePath.'/Resources/config/services/'.$this->entityLC.'.yml';
        
        if (!file_exists($targetFile)) {
            $this->createService($parameters, $targetFile);
       
            return true;
        }

        $parser = new Parser();

        $currentFileArray = $parser->parse(file_get_contents($targetFile));
        
        if (!empty($currentFileArray['services'][$this->bundleAlias.'.'.$this->entityLC.'_manager'])) {         
            return true;
        }
        
        $partialFile = $this->bundlePath.'/Resources/config/servicesPartial.yml';
        
        // generate partial                
        $this->renderFile('Resources/config/services/servicesPartial.yml', $partialFile, $parameters); 
        
        $currentCode = file_get_contents($currentFile);
        $partialCode = file_get_contents($partialFile);
        
        $code = $currentCode;
        $code .= $partialCode;
        
        unlink($partialFile);
        
        if (false === file_put_contents($currentFile, $code)) {
            throw new \RuntimeException('Could not write to services.yml');
        }                
        return true;        
    }   

}
