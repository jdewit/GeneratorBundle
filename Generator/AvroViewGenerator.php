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
    /**
     * Generates the entity class if it does not exist.
     *
     */
    public function generate()
    {
        switch ($this->style) {
            //knockout
            case 'knockout':
                $views = array('list', 'table', 'form', 'getForm', 'search');
                 foreach ($views as $view) {
                    $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig: ');
                    try {
                        $this->generateKnockoutView($view);
                        $this->output->writeln('<info>Ok</info>');
                    } catch (\RuntimeException $e) {
                        $this->output->writeln(array(
                            '<error>Fail</error>',
                            $e->getMessage(),
                            ''
                        ));
                    }  
                }
                $this->output->write('Generating '.$this->bundleBasename.'/Resources/assets/js/knockoutjs/'.$this->entity.'/Model.js: ');
                try {
                    $this->generateKnockoutViewModel();
                    $this->output->writeln('<info>Ok</info>');
                } catch (\RuntimeException $e) {
                    $this->output->writeln(array(
                        '<error>Fail</error>',
                        $e->getMessage(),
                        ''
                    ));
                }  
                $this->output->write('Generating '.$this->bundleBasename.'/Resources/assets/js/knockoutjs/'.$this->entity.'/ListModel.js: ');
                try {
                    $this->generateKnockoutListModel();
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
                foreach ($this->parameters['actions'] as $view) {
                    $this->output->write('Generating '.$this->bundleBasename.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig: ');
                    try {
                        $this->generateView($view);
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
     * @param $view The view to generate
     */
    private function generateView($view)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig';

        $this->renderFile('Resources/views/entity/'.$view.'.html.twig', $filename);
    }
    
    /**
     * Generates knockoutjs views.
     * 
     * @param $view The view to generate
     */
    private function generateKnockoutView($view)
    {
        $filename = $this->bundlePath.'/Resources/views/'.$this->entity.'/'.$view.'.html.twig';

        $this->renderFile('Resources/views/entity/knockoutjs/'.$view.'.html.twig', $filename);
    }

    /**
     * Generates knockoutjs viewModel.
     * 
     */
    private function generateKnockoutViewModel()
    {
        $filename = $this->bundlePath.'/Resources/assets/js/knockoutjs/'.$this->entityCC.'Model.js';

        $this->renderFile('Resources/assets/js/knockoutjs/model.html.twig', $filename);
    }

    /**
     * Generates knockoutjs listModel.
     * 
     * @param array $this->parameters The this->parameters needed to generate the file
     */
    private function generateKnockoutListModel()
    {
        $filename = $this->bundlePath.'/Resources/assets/js/knockoutjs/'.$this->entityCC.'ListModel.js';

        $this->renderFile('Resources/assets/js/knockoutjs/listModel.html.twig', $filename);
    }

}
