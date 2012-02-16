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
 * Generates the bundles readme.md.
 *
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroReadmeGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     */
    public function generate()
    {
        $this->output->writeln('Generating '.$this->parameters['bundle_name'].'/README.md: <info>OK</info>');
        try {
            $this->generateReadme();        
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }    
    }
    
    /*
     * Generate the readme.md
     * 
     */
    private function generateReadme()
    {   
        $filename = $this->bundlePath.'/README.md';

        $this->renderFile('README.md', $filename);                      
    }    
}
