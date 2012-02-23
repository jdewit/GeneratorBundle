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
 * Generates behat features based on a Doctrine entity.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroFeatureGenerator extends Generator
{
    /**
     * Generates the features.
     */
    public function generate()
    {

        $this->output->write('Generating '.$this->bundleName.'/Features/'.$this->entityCC.'.feature: ');
        try {
            $this->generateFeatures();
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
     * Write the feature file.
     */
    private function generateFeatures()
    {   
        $filename = $this->bundlePath.'/Features/'.$this->entityCC.'.feature';      
        
        if ($this->style == 'knockout') {
            $this->renderFile('Features/knockout.feature', $filename);
        } else {
            $this->renderFile('Features/entity.feature', $filename);
        }
    }    
    
}
