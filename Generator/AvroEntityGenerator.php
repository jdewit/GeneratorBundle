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
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\EntityRepositoryGenerator;
use Doctrine\ORM\Tools\Export\ClassMetadataExporter;

/**
 * Generates model, modelInterface, modelManager, modelManagerInterface based on a Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jonathan H. Wage <jonwage@gmail.com> 
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroEntityGenerator extends Generator
{
    protected $entity;
    protected $entityLC;
    protected $fields;
    
    public function generate($entity, array $fields, $writeManager = true)
    {
        $this->entity = $entity;
        $this->entityLC = strtolower($entity);
        $this->fields = $fields;
        $parameters = array(
            'entity' => $this->entity,
            'entity_lc' => $this->entityLC,
            'entity_class' => $this->bundleNamespace.'\\Entity\\'.$this->entity,
            'fields' => $this->fields,
            'bundle_basename' => $this->bundleBasename,
            'bundle_name' => $this->bundleName,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,
            'bundle_vendor' => $this->bundleVendor,
            'bundle_alias' => $this->bundleAlias,     
            'bundle_corename' => $this->bundleCorename,     
            'db_driver' => $this->dbDriver
        );

        $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'.php: ');        
        try {
            $this->generateEntity($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }        
        
        $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'Interface.php: ');                
        try {
            $this->generateEntityInterface($parameters);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }  
        
        if ($writeManager) {
            $this->output->write('Generating '.$this->bundleName.'/Entity/Manager/'.$this->entity.'Manager.php: ');        
            try {
                $this->generateEntityManager($parameters);
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }  

            $this->output->write('Generating '.$this->bundleName.'/Entity/Manager/'.$this->entity.'ManagerInterface.php: ');       
            try {
                $this->generateEntityManagerInterface($parameters);
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

    /*
     * Generates the Entity code
     * 
     * @param array $parameters The parameters needed to generate the file
     */
    private function generateEntity($parameters)
    {
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'.php';

        $this->renderFile('Entity/entity.php', $filename, $parameters); 
    }
    
    /*
     * Generate the EntityInterface
     * 
     * @param array $parameters The parameters needed to generate file
     */
    private function generateEntityInterface($parameters)
    {
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'Interface.php';

        $this->renderFile('Entity/entityInterface.php', $filename, $parameters);        
    }
    
    /*
     * Generate the EntityManager
     * 
     * @param array $parameters The parameters needed to generate file
     */
    private function generateEntityManager($parameters)
    {   
        $filename = $this->bundlePath.'/Entity/Manager/'.$this->entity.'Manager.php';      
        
        $this->renderFile('Entity/manager.php', $filename, $parameters);        
               
    }
    
    /*
     * Generate the EntityManagerInterface
     * 
     * @param array $parameters The parameters needed to generate filee
     */
    private function generateEntityManagerInterface($parameters)
    {
        $filename = $this->bundlePath.'/Entity/Manager/'.$this->entity.'ManagerInterface.php';

        $this->renderFile('Entity/managerInterface.php', $filename, $parameters);
    }
    

}
