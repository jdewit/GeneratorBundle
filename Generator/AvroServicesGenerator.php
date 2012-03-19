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

/**
 * Generates a configuration file for various services for an entity.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroServicesGenerator extends Generator
{
    /**
     * Generate configuration file .
     *
     */
    public function generate()
    {
        if (!$this->serviceConfigFormat) {
            $this->output->writeln(array(
                '',
                'Specify the service configuration format. [yml]',
            ));

            $this->serviceConfigFormat = $this->dialog->ask($this->output, $this->dialog->getQuestion('Bundle service configuration format?', 'yml', ':'), 'yml');
        }

        $target = $this->bundlePath.'/Resources/config/services/'.$this->entityCC.'.'.$this->serviceConfigFormat;
        $this->output->write('Creating '.$target. ':');
        try {
            $this->createService($target);
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
     * render the configuration file
     *
     * @param string $target filename to be rendered
     * @param string $format configuration format
     */
    protected function createService($target)
    {
        switch($this->serviceConfigFormat) {
            case 'yml':
                $this->renderFile('Resources/config/services/service.yml', $target);
            break;
            case 'xml':
                //TODO
                //$this->renderFile('Resources/config/services/service.xml', $target);
            break;
        }
    }
}
