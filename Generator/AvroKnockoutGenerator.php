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
 * @author Joris de Wit <joris.w.Avro@gmail.com>
 */
class AvroKnockoutGenerator extends Generator
{
    /**
     * Generates the entity class if it does not exist.
     *
     */
    public function generate()
    {
        $write = true;
        if (file_exists($this->bundlePath.'/Resources/assets/js/knockoutjs/'.$this->entityCC.'Model.js')) {
            $write = false;
            if ($this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Overwrite '.$this->entity.'Model and '.$this->entity.'ListModel', 'no', '?'), false)) {
                $write = true;
            }
        }

        if ($write) {
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
        }

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
