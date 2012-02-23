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
    public function generate()
    {
        if ($this->thirdParty == true) {
            $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'.php: ');        
            try {
                $this->generateEntity();
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
                $this->generateEntityInterface();
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }  

            if ($dialog->askConfirmation($output, $dialog->getQuestion('Overwrite '.$entity.'Manager', 'no', '?'), false)) {
                $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'Manager.php: ');        
                try {
                    $this->generateEntityManager();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                }  

                $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'ManagerInterface.php: ');       
                try {
                    $this->generateEntityManagerInterface();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                } 
            }
        } else {
            $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'.php: ');        
            try {
                $this->generateEntity();
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }        
            
            if (file_exists($this->bundlePath.'/Entity/'.$this->entityCC.'Manager.php')) {
                $write = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion($this->entityCC.'.yml exists. Overwrite?', 'no', '?'), false);
            } else {
                $write = true;
            }

            if ($write) {
                $this->output->write('Generating '.$this->bundleName.'/Entity/'.$this->entity.'Manager.php: ');        
                try {
                    $this->generateEntityManager();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                }  

            } else {
                $this->output->writeln('<info>Ok</info>');
            }
        }
        // update the database
        if ($this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Update the database', 'yes', '?'), true)) {
            $this->output->write('Updating database: ');        
            try {
                $this->runConsole("doctrine:schema:update", array("--force" => true));
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
     * @param array $this->parameters The this->parameters needed to generate the file
     */
    private function generateEntity()
    {
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'.php';

        $this->renderFile('Entity/entity.php', $filename); 
    }
    
    /*
     * Generate the EntityInterface
     * 
     */
    private function generateEntityInterface()
    {
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'Interface.php';

        $this->renderFile('Entity/entityInterface.php', $filename);        
    }
    
    /*
     * Generate the EntityManager
     * 
     */
    private function generateEntityManager()
    {   
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'Manager.php';      
        
        $this->renderFile('Entity/manager.php', $filename);        
               
    }
    
    /*
     * Generate the EntityManagerInterface
     * 
     */
    private function generateEntityManagerInterface()
    {
        $filename = $this->bundlePath.'/Entity/'.$this->entity.'ManagerInterface.php';

        $this->renderFile('Entity/managerInterface.php', $filename);
    }
    

}
