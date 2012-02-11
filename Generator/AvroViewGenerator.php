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
            'style' => $this->style,
            'actions' => array('list', 'new', 'edit', 'table', 'form')
        );          
        
        switch ($this->style) {
            //knockout
            case '2':
                 foreach ($parameters['actions'] as $view) {
                    $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig: ');
                    try {
                        $this->generateKnockoutView($parameters, $view);
                        $this->output->writeln('<info>Ok</info>');
                    } catch (\RuntimeException $e) {
                        $this->output->writeln(array(
                            '<error>Fail</error>',
                            $e->getMessage(),
                            ''
                        ));
                    }  
                }
                $this->output->write('Generating '.$this->bundleBasename.'/Resources/assets/js/knockoutjs/'.$this->entity.'/'.$view.'.js: ');
                try {
                    $this->generateKnockoutViewModel($parameters, $view);
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
                foreach ($parameters['actions'] as $view) {
                    $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig: ');
                    try {
                        $this->generateView($parameters, $view);
                        $this->output->writeln('<info>Ok</info>');
                    } catch (\RuntimeException $e) {
                        $this->output->writeln(array(
                            '<error>Fail</error>',
                            $e->getMessage(),
                            ''
                        ));
                    }  
                }
            break;
        }
        
    }
    
    /**
     * Generates the view template in the final bundle.
     * 
     * @param array $parameters The parameters needed to generate the file
     * @param $view The view to generate
     */
    private function generateView($parameters, $view)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig';

        $this->renderFile('Resources/views/entity/'.$view.'.html.twig', $filename, $parameters);
    }
    
    /**
     * Generates knockoutjs views.
     * 
     * @param array $parameters The parameters needed to generate the file
     * @param $view The view to generate
     */
    private function generateKnockoutView($parameters, $view)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig';

        $this->renderFile('Resources/views/entity/knockoutjs/'.$view.'.html.twig', $filename, $parameters);
    }

    /**
     * Generates knockoutjs viewModel.
     * 
     * @param array $parameters The parameters needed to generate the file
     * @param $view The view to generate
     */
    private function generateKnockoutViewModel($parameters, $view)
    {
        $filename = $this->bundlePath.'/Resources/assets/js/knockoutjs/'.$this->entityLC.'Model.js';

        $this->renderFile('Resources/assets/js/knockoutjs/model.html.twig', $filename, $parameters);
    }

}
