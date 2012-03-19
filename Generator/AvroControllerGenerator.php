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
use Avro\GeneratorBundle\Manipulator\RoutingManipulator;

/**
 * Generates a Avro controller.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroControllerGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     */
    public function generate()
    {
        $this->output->write('');

        if (!$this->routingFormat) {
            $this->routingFormat = $this->dialog->ask($this->output, $this->dialog->getQuestion('Enter the bundles routing file format', 'yml', ':'), 'yml');
        }

        switch ($this->style) {
            case 'knockout': 
                $this->output->write('Generating '.$this->bundleName.'/Controller/'.$this->entity.'Controller.php: ');
                try {
                    $this->generateKnockoutController();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                }      
            break;
            default:
                $this->output->write('Generating '.$this->bundleName.'/Controller/'.$this->entity.'Controller.php: ');
                try {
                    $this->generateControllerClass();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                }      
            break;
        }

        $this->output->write('Adding controller routing to '.$this->bundleName.'/app/config/routing.'.$this->routingFormat.': ');
        try {
            $this->UpdateRouting();
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
     * Generates a default controller class 
     *
     */
    private function generateControllerClass()
    {
        $filename = $this->bundlePath.'/Controller/'.$this->entity.'Controller.php';
        
        $this->renderFile('Controller/controller.php', $filename);
    }

    /**
     * Generates a knockoutjs controller.
     * 
     */
    private function generateKnockoutController()
    {
        $filename = $this->bundlePath.'/Controller/'.$this->entity.'Controller.php';
        
        $this->renderFile('Controller/KnockoutController.php', $filename);
    }

    /*
     * Update bundle routing file
     *
     * @param $format
     */
    protected function updateRouting()
    {
        $filename = $this->bundlePath.'/Resources/config/routing.'.$this->routingFormat;

        $routingManipulator = new RoutingManipulator($filename, $this->routingFormat);
        $routingManipulator->updateBundleRouting($this->bundleName, $this->bundleAlias, $this->entityUS, $this->entity);
    }
}
