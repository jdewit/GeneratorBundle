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
use Avro\GeneratorBundle\Generator\AvroFormGenerator;
use Avro\GeneratorBundle\Generator\AvroServicesGenerator;
use Avro\GeneratorBundle\Generator\AvroViewGenerator;


/**
 * Generates a form type class for a given Doctrine entity.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Hugo Hamon <hugo.hamon@sensio.com>
 */
class GenerateAvroCrudCommand extends GenerateAvroCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('generate:avro:crud')
            ->setAliases(array('generate:avro:crud'))
            ->setDescription('Generates crud in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)');
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        
        $dialog->writeSection($output, 'Welcome to the Avro crud generator!');

        list($bundle, $entities, $style, $overwrite) = $this->baseCommand($input, $output, $dialog);

        foreach($entities as $entity) {
            $fields = $entity['fields'];
            $entity = $entity['name'];

            //Generate Controller file
            $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroControllerGenerator->generate();

            //Generate View files
            $avroViewGenerator = new AvroViewGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroViewGenerator->generate();        
            
            //Generate Form files
            $avroFormGenerator = new AvroFormGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroFormGenerator->generate();

            //Update services.yml
            $avroServicesGenerator = new AvroServicesGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroServicesGenerator->generate();        
        }
        
        $output->writeln('CRUD created succesfully!');
    }

}
