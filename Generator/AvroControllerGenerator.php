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
    protected $entity;
    protected $entityLC;
    
    /**
     * Generates the entity class if it does not exist.
     *
     * @param string $entity The entity relative class name
     * @param array $fields The entity fields
     */
    public function generate($entity)
    {
        if ($entity) {
            $this->entity = $entity;
            $this->entityLC = strtolower($entity);
        } else {
            $this->output->writeln(array(
                '',
                'Enter the controllers name. (ex. admin)',
            ));
            $this->entityLC = strtolower($this->dialog->ask($this->output, '<info>Controller name:</info> '));
            $this->entity = ucfirst($this->entityLC);
        }
        
        $this->output->writeln('');
        if ($this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Generate default controller actions? [list, show, new, edit, delete, batch]', 'yes', '?'), true)) {
            $actions =  array('list', 'show', 'new', 'edit', 'delete', 'batch', 'getJson');
        } else {
            while(true) {
                $this->output->writeln(array(
                    '',
                    'Enter the controllers actions. Just press enter when finished.'
                ));
                $action = $this->dialog->ask($this->output, '<info>Controller action:</info> '); 
                if (empty($action)) {
                    break;
                }
                $actions[] = $action;
            }
        }

        $this->output->write('');
        $routingFormat = $this->dialog->ask($this->output, $this->dialog->getQuestion('Enter the bundles routing file format', 'yml', ':'), 'yml');
        
        $parameters = array(
            'entity' => $this->entity,
            'entity_lc' => $this->entityLC,
            'bundle_name' => $this->bundleName,
            'bundle_corename' => $this->bundleCorename,
            'bundle_path' => $this->bundlePath,
            'bundle_namespace' => $this->bundleNamespace,  
            'bundle_alias' => $this->bundleAlias,          
            'db_driver' => $this->dbDriver,
            'actions' => $actions,
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

        $this->output->write('Adding controller routing to '.$this->bundleName.'/app/config/routing.'.$routingFormat.': ');
        try {
            $this->UpdateRouting($routingFormat);
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

    protected function updateRouting($format)
    {
        $filename = $this->bundlePath.'/Resources/config/routing.'.$format;

        $routingManipulator = new RoutingManipulator($filename, $format);
        $routingManipulator->updateBundleRouting($this->bundleName, $this->bundleAlias, $this->entity);
    }
}
