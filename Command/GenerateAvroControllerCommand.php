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
use Avro\GeneratorBundle\Generator\AvroControllerGenerator;
use Avro\GeneratorBundle\Generator\AvroViewGenerator;


/**
 * Generates a form type class for a given Doctrine entity.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class GenerateAvroControllerCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:controller')
            ->setAliases(array('generate:avro:controller'))
            ->setDescription('Generates controller in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        
        $dialog->writeSection($output, 'Welcome to the Avro controller generator!');

        $output->writeln('');
        if ($dialog->askConfirmation($output, $dialog->getQuestion('Is this controller based on an entity?', 'yes', '?'), true)) {           

            // initiate base command
            list($bundle, $entity, $fields, $style, $overwrite) = $this->baseCommand($input, $output, $dialog);

        } else {
            $output->writeln(array(
                '',
                'Enter the full name of the controllers bundle. (ie. FOSUserBundle, AvroCalendarBundle, etc)'
            ));
            $bundleName = $dialog->ask($output, '<info>Bundle name:</info> ');
            $bundle = Validators::validateBundleName($bundleName);
            $entity = false;
        }

        //Generate Controller file
        $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroControllerGenerator->generate();
        
        $output->writeln('Controller created succesfully!');
    }

}
