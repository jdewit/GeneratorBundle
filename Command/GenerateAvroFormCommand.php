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
use Avro\GeneratorBundle\Generator\AvroFormGenerator;


/**
 * Generates a form type class for a given Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class GenerateAvroFormCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:form')
            ->setAliases(array('generate:avro:form'))
            ->setDescription('Generates form code in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Avro form generator!');

        while (true) {
            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));

            list($bundle, $entity) = $this->parseShortcutNotation($entity);
            $output->writeln($bundle);
            $output->writeln($entity);
            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }
            $output->writeln($b->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php');
            if (!file_exists($b->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                $output->writeln($entity.' not found. Please create it.');
                $output->writeln('<error>Command aborted</error>');

                return 1;              
            }

            break;         
        }

        $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundle).'\\'.$entity;
        $metadata = $this->getEntityMetadata($entityClass);
        $fields = $this->getFieldsFromMetadata($metadata[0]);
        $bundle   = $this->getApplication()->getKernel()->getBundle($bundle);

        //Generate Form files
        $avroFormGenerator = new AvroFormGenerator($container, $dialog, $output, $bundle);
        $avroFormGenerator->generate($entity, $fields);

        $output->writeln('Form created');
    }
    
}
