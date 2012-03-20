<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Avro\GeneratorBundle\Generator\AvroKnockoutGenerator;


/**
 * Generates a views for a given Doctrine entity.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class GenerateAvroKnockoutCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:knockout')
            ->setAliases(array('generate:avro:knockout'))
            ->setDescription('Generates views in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        
        $dialog->writeSection($output, 'Welcome to the Avro knockout generator!');

        list($bundle, $entities, $style, $overwrite) = $this->baseCommand($input, $output, $dialog);

        foreach($entities as $entity) {
            $fields = $entity['fields'];
            $entity = $entity['name'];

            //Generate Knockout model files
            $avroKnockoutModelGenerator = new AvroKnockoutModelGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroKnockoutModelGenerator->generate();        
        }
        
        $output->writeln('Knockout models created succesfully!');
    }

}
