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
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Avro\GeneratorBundle\Generator\AvroImportHandlerGenerator;


/**
 * Generates an import handler class for a given Doctrine entity.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class GenerateAvroImportCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:import')
            ->setAliases(array('generate:avro:import'))
            ->setDescription('Generates an import handler in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('style', null, InputOption::VALUE_REQUIRED, 'The style of code you would like to generate.');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        
        $dialog->writeSection($output, 'Welcome to the Avro Import generator!');
        
        // initiate base command
        list($bundle, $entity, $fields, $style, $overwrite) = $this->baseCommand($input, $output, $dialog);

        // confirm
        $dialog->writeSection($output, 'Generating import code for '. $bundle->getName() );

        //Generate Import Handler files
        $avroImportHandlerGenerator = new AvroImportHandlerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroImportHandlerGenerator->generate();
    }
    
}
