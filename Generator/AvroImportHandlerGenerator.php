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
 * Generates a CSV Import Handler based on a Doctrine entity.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroImportHandlerGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     * @param string $entity The entity relative class name
     * @param array $fields The entity fields
     */
    public function generate()
    {
        $this->output->write('Generating '.$this->bundleName.'/Import/Handler/'.$this->entity.'ImportHandler.php: ');
        try {
            $this->generateImportHandler();
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
     * Generates the Import Handler in the final bundle.
     *
     */
    private function generateImportHandler()
    {   
        $filename = $this->bundlePath.'/Import/Handler/'.$this->entity.'ImportHandler.php';      
        
        $this->renderFile('Import/Handler/ImportHandler.php', $filename);
    }    
}
