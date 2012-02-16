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
class AvroFormGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     * @param string $entity The entity relative class name
     * @param array $fields The entity fields
     */
    public function generate()
    {
        $this->output->write('Generating '.$this->bundleName.'/Form/'.$this->entity.'FormType.php: ');
        try {
            $this->generateFormType();
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
            $this->generateFormHandler();
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
     */
    private function generateFormType()
    {   
        $filename = $this->bundlePath.'/Form/Type/'.$this->entity.'FormType.php';      
        
        $this->renderFile('Form/Type/FormType.php', $filename);
    }    
    
    /**
     * Generates the FormHandler in the final bundle.
     *
     */
    private function generateFormHandler()
    {
        $filename = $this->bundlePath.'/Form/Handler/'.$this->entity.'FormHandler.php';             
        
        $this->renderFile('Form/Handler/FormHandler.php', $filename);       
    }       
    
}
