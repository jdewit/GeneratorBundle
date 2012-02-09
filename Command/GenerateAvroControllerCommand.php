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
        if ($dialog->askConfirmation($output, $dialog->getQuestion('Is this controller tied to an entity?', 'no', '?'), false)) {           
            $output->writeln('(ex. AcmeTestBundle:Blog)');
            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Avro\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));         

            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundle).'\\'.$entity;
            $metadata = $this->getEntityMetadata($entityClass);
            $fields = $this->getFieldsFromMetadata($metadata[0]);

        } else {
            $output->writeln(array(
                '',
                'Enter the full name of the controllers bundle. (ie. FOSUserBundle, AvroCalendarBundle, etc)'
            ));
            $bundleName = $dialog->ask($output, '<info>Bundle name:</info> ');
            $bundle = Validators::validateBundleName($bundleName);
            $entity = false;
        }

        $bundle = $this->getApplication()->getKernel()->getBundle($bundle);

        //Generate Controller file
        $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle);
        $avroControllerGenerator->generate($entity);
        
    }

}
