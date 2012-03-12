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
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\DBAL\Types\Type;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;
use Avro\GeneratorBundle\Command\GenerateDoctrineCommand;
use Avro\GeneratorBundle\Command\Validators;
use Avro\GeneratorBundle\Manipulator\RoutingManipulator;
use Avro\GeneratorBundle\Generator\AvroEntityGenerator;
use Avro\GeneratorBundle\Generator\AvroConfigGenerator;
use Avro\GeneratorBundle\Generator\AvroControllerGenerator;
use Avro\GeneratorBundle\Generator\AvroViewGenerator;
use Avro\GeneratorBundle\Generator\AvroKnockoutGenerator;
use Avro\GeneratorBundle\Generator\AvroFormGenerator;
use Avro\GeneratorBundle\Generator\AvroReadmeGenerator;
use Avro\GeneratorBundle\Generator\AvroFeatureGenerator;
use Avro\GeneratorBundle\Generator\AvroServicesGenerator;
use Avro\GeneratorBundle\Generator\AvroImporterGenerator;

/**
 * Generates entity, controller, form, view, and configuration code in a bundle.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.Avro.com>
 */
class GenerateAvroAllCommand extends GenerateAvroCommand
{     
    protected function configure()
    {
        $this
            ->setName('generate:avro:all')
            ->setAliases(array('generate:avro:all'))
            ->setDescription('Generates entity, controller, form, view, and configuration code in a bundle.')
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
        
        // welcome
        $dialog->writeSection($output, 'Welcome to the Avro create all generator!');

        // initiate base command
        list($bundle, $entity, $fields, $style, $overwrite) = $this->baseCommand($input, $output, $dialog);

        // add fields
        list($fields) = $this->fieldGenerator($input, $output, $dialog, $entity, $fields);      

        // confirm
        $dialog->writeSection($output, 'Generating code for '. $bundle->getName() );
                       
        //Generate Bundle/Entity files
        $avroEntityGenerator = new AvroEntityGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);    
        $avroEntityGenerator->generate();  
        
        //Generate Controller file
        $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroControllerGenerator->generate();

        //Generate View files
        $avroViewGenerator = new AvroViewGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroViewGenerator->generate();    
        
        if ($style == 'knockout') {
            //Generate Knockout model files
            $avroKnockoutGenerator = new AvroKnockoutGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroKnockoutGenerator->generate();  
        }
        
        //Generate Form files
        $avroFormGenerator = new AvroFormGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroFormGenerator->generate();

        //Generate importer
        $avroImporterGenerator = new AvroImporterGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroImporterGenerator->generate();  
        
        //Generate Feature files
        $avroFeatureGenerator = new AvroFeatureGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroFeatureGenerator->generate();

        //Update services.yml
        $avroServicesGenerator = new AvroServicesGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
        $avroServicesGenerator->generate();     

        $dialog->writeSection($output, 'Everything was created succesfully!');
    }
}
