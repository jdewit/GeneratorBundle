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

        list($bundle, $entity, $fields, $style) = $this->baseCommand($input, $output, $dialog);

        //Generate Feature files
        $avroFeatureGenerator = new AvroFeatureGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style);
        $avroFeatureGenerator->generate();

        $output->writeln('Behat features created succesfully!');
    }
    
}
