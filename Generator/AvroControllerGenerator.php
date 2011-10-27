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
 * Generates a Avro controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroControllerGenerator extends Generator
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
            'fields' => $this->fields,
            'bundle_name' => $this->bundleName,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,  
            'bundle_alias' => $this->bundleAlias,          
            'db_driver' => $this->dbDriver,
            'actions' => array('list', 'new', 'edit', 'delete')
        );

        
        $this->output->write('Generating '.$this->bundleName.'/Controller/'.$this->entity.'Controller.php: ');
        try {
            $this->generateControllerClass($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }      

        $this->output->write('Adding controller routing to '.$this->bundleName.'/app/config/routing.yml: ');
        try {
            $this->UpdateRouting($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }          
        //$this->generateTestClass();
        //$this->output->writeln('Generating '.$this->bundleName.'/Entity/'.$this->entity.'Manager.php: <info>OK</info>');


    }

    /**
     * Generates the controller class only.
     *
     */
    private function generateControllerClass($parameters)
    {
        $filename = $this->bundlePath.'/Controller/'.$this->entity.'Controller.php';
        
        $this->renderFile('Controller/controller.php', $filename, $parameters);
    }

    protected function updateRouting()
    {
        $filename = $this->bundlePath.'/Resources/config/routing.yml';
        $current = file_get_contents($filename);
        $code = $this->bundleAlias.'_'.$this->entity.':';
        $code .= "\n";
        $code .= sprintf("    resource: \"@%s/Controller/%sController.php\"", $this->bundleName, $this->entity);
        $code .= "\n";
        $code .= sprintf("    type:     annotation ");
        $code .= "\n \n";
        $code .= $current;

        if (false === file_put_contents($filename, $code)) {
            throw new \RuntimeException('Could not write to routing.yml');
        }

        return true;        
    }
}
