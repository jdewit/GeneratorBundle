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
use Avro\GeneratorBundle\Manipulator\ConfigManipulator;

/**
 * Generates a services for an entity.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroServicesGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     */
    public function generate()
    {
        $fileExists = false;
        $overwrite = true;

        if (file_exists($this->bundlePath.'/Resources/config/services/'.$this->entityCC.'.yml')) {
            $fileExists = true;
            if ($this->dialog->askConfirmation($this->output, $this->dialog->getQuestion($this->entityCC.'.yml exists. Overwrite?', 'no', '?'), false)) {
                $overwrite = true;
            } 
        } 

        if (!$fileExists) {
            $this->output->writeln(array(
                '',
                'Specify the service configuration of your bundle.',
                '[config.yml is currently only method supported]',
                'WARNING: This will overwrite the configuration file.',
            ));

            $format = $this->dialog->ask($this->output, $this->dialog->getQuestion('Bundle services format?', 'config.yml', ':'), 'config.yml');

            $this->output->write('Updating bundle services config:');
            try {
                $this->updateBundleServicesConfig($format);
                $this->output->writeln('<info>Ok</info>');
            } catch (\RuntimeException $e) {
                $this->output->writeln(array(
                    '<error>Fail</error>',
                    $e->getMessage(),
                    ''
                ));
            }  
        }
        if (!$fileExists || $overwrite) {
            $targetFile = $this->bundlePath.'/Resources/config/services/'.$this->entityCC.'.yml';
            $this->output->write('Creating '.$targetFile. ':');
            try {
                $this->createService($targetFile);
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

    protected function updateBundleServicesConfig($format)
    {
        switch ($format) {
            case 'config.yml':
                $configManipulator = new ConfigManipulator($this->bundlePath.'/Resources/config/config.yml');
                $configManipulator->addResourceToImports($this->bundleName.'/Resources/config/services/'.$this->entityCC.'.yml');
            break;
        }
    }

    protected function createService($targetFile)
    {
        $this->renderFile('Resources/config/services/servicesPartial.yml', $targetFile);
    }

//    protected function updateService()
//    {
//        
//
//        $parser = new Parser();
//
//        $currentFileArray = $parser->parse(file_get_contents($targetFile));
//        
//        if (!empty($currentFileArray['services'][$this->bundleAlias.'.'.$this->entityCC.'_manager'])) {         
//            return true;
//        }
//        
//        $partialFile = $this->bundlePath.'/Resources/config/servicesPartial.yml';
//        
//        // generate partial                
//        $this->renderFile('Resources/config/services/servicesPartial.yml', $partialFile); 
//        
//        $currentCode = file_get_contents($currentFile);
//        $partialCode = file_get_contents($partialFile);
//        
//        $code = $currentCode;
//        $code .= $partialCode;
//        
//        unlink($partialFile);
//        
//        if (false === file_put_contents($currentFile, $code)) {
//            throw new \RuntimeException('Could not write to services.yml');
//        }                
//        return true;        
//    }   

}
