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
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\DBAL\Types\Type;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;
use Avro\GeneratorBundle\Command\Validators;
use Avro\GeneratorBundle\Generator\AvroEntityGenerator;

/**
 * Generates entity code in a bundle.
 *
 * @author Joris de Wit <joris.w.Avro.com>
 */
class GenerateAvroEntityCommand extends GenerateAvroCommand
{     
    protected function configure()
    {
        $this
            ->setName('generate:avro:entity')
            ->setAliases(array('generate:avro:entity'))
            ->setDescription('Generates entity code in a bundle.')
            ->addOption('entity', null, InputOption::VALUE_REQUIRED, 'The entity class name to initialize (shortcut notation)')
            ->addOption('fields', null, InputOption::VALUE_REQUIRED, 'The fields to create with the new entity')   
            ->addOption('dbDriver', null, InputOption::VALUE_REQUIRED, 'The database you are using (orm, couchdb, mongodb', 'orm')     
            ->setHelp(<<<EOT
The <info>generate:avro:all</info> task generates entity, controller, form, view, and configuration code in a bundle.
EOT
        );
    }

    /**
     * @throws \InvalidArgumentException When the bundle doesn't end with Bundle (Example: "Bundle/MySampleBundle")
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $container = $this->getContainer();
        $dialog = $this->getDialogHelper();
        
        $dialog->writeSection($output, 'Welcome to the Avro entity generator!');
        
        // initiate base command
        list($bundle, $entity, $fields, $style) = $this->baseCommand($input, $output, $dialog);
        list($fields) = $this->fieldGenerator($input, $output, $dialog, $entity, $fields);      

        // confirm
        $dialog->writeSection($output, 'Generating code for '. $bundle->getName() );
                       
        //Generate Bundle/Entity files
        $avroEntityGenerator = new AvroEntityGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style);    
        $avroEntityGenerator->generate();  

        $output->writeln('Entity created succesfully!');
    }
}
