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
use Avro\GeneratorBundle\Generator\AvroFeatureGenerator;


/**
 * Generates features for a given Doctrine entity.
 *
 */
class GenerateAvroFeatureCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:feature')
            ->setAliases(array('generate:avro:feature'))
            ->setDescription('Generates feature code in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Avro feature generator!');

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
        $avroFeatureGenerator = new AvroFeatureGenerator($container, $dialog, $output, $bundle);
        $avroFeatureGenerator->generate($entity, $fields);
        $output->writeln('Features created');
   
        
        $output->writeln('Service configuration created succesfully!');

    }
    
}
