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
 * Generates an entities views.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroViewGenerator extends Generator
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
            'bundle_vendor' => $this->bundleVendor,
            'bundle_alias' => $this->bundleAlias,  
            'bundle_alias_cc' => $this->bundleAliasCC,
            'db_driver' => $this->dbDriver,
            'actions' => array('list', 'new', 'edit', 'delete')
        );          
        
        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/list.html.twig: ');
        try {
            $this->generateListView($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  

        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'new.html.twig: ');
        try {     
            $this->generateNewView($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  
        
        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/edit.html.twig: ');
        try {     
            $this->generateEditView($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  

        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/show.html.twig: ');
        try {     
            $this->generateShowView($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        } 

        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/form.html.twig: ');
        try {     
            $this->generateFormView($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  

        $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/table.html.twig: ');
        try {
            $this->generateTableView($parameters);
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
     * Generates the list.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the file
     */
    private function generateListView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/list.html.twig';

        $this->renderFile('Resources/views/entity/list.html.twig', $filename, $parameters);
    }

    /**
     * Generates the new.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the fi
     */
    private function generateNewView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/new.html.twig';   

        $this->renderFile('Resources/views/entity/new.html.twig', $filename, $parameters);
    }

    /**
     * Generates the edit.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the fi
     */
    private function generateEditView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/edit.html.twig';    

        $this->renderFile('Resources/views/entity/edit.html.twig', $filename, $parameters);
    }

    /**
     * Generates the show.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the fi
     */
    private function generateShowView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/show.html.twig';    

        $this->renderFile('Resources/views/entity/show.html.twig', $filename, $parameters);
    }

    /**
     * Generates the form.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the file
     */
    private function generateFormView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/form.html.twig';  

        $this->renderFile('Resources/views/entity/form.html.twig', $filename, $parameters);
    }
    

    /**
     * Generates the table.html.twig template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the file
     */
    private function generateTableView($parameters)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/table.html.twig';  

        $this->renderFile('Resources/views/entity/table.html.twig', $filename, $parameters);
    }        
    
}
