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
use Avro\GeneratorBundle\Generator\AvroKnockoutModelGenerator;
use Avro\GeneratorBundle\Generator\AvroFormGenerator;
use Avro\GeneratorBundle\Generator\AvroReadmeGenerator;
use Avro\GeneratorBundle\Generator\AvroFeatureGenerator;
use Avro\GeneratorBundle\Generator\AvroServicesGenerator;
use Avro\GeneratorBundle\Generator\AvroImportHandlerGenerator;

/**
 * Generates code for all of the mapped entities in an application
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.Avro.com>
 */
class GenerateAvroBuildCommand extends GenerateAvroCommand
{     
    protected $em;

    protected function configure()
    {
        $this
            ->setName('generate:avro:build')
            ->setAliases(array('generate:avro:build'))
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

        $style = $dialog->askAndValidate($output, $dialog->getQuestion('Enter code style you would like to generate. (1. default, 2. knockout)', '1. default', '?'), array('Avro\GeneratorBundle\Command\Validators', 'validateStyle'), '2'); 
        $routingFormat = $dialog->ask($output, $dialog->getQuestion('Enter the bundles routing file format', 'yml', ':'), 'yml');
        $serviceConfigFormat = $dialog->ask($output, $dialog->getQuestion('Enter the bundles service configuration format', 'yml', ':'), 'yml');
        $overwrite = $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to overwrite existing files', 'no', '?'), false);

        if (!$dialog->askConfirmation($output, $dialog->getQuestion('Are you sure you want to generate code for all of your mapped entities', 'yes', '?'), true)) {
            return false;
        }

        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $cmf = $this->em->getMetadataFactory();
        $metadatas = $cmf->getAllMetadata();
        foreach($metadatas as $metadata) {
            $entityNamespaceArray = explode("\\", $metadata->getName()); 
            $vendor = $entityNamespaceArray[0];
            $bundleName = $vendor.$entityNamespaceArray[1];
            $entity = $entityNamespaceArray[3];
            $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
            $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundleName).'\\'.$entity;
            $entityMetadata = $this->getEntityMetadata($entityClass);
            $fields = $this->getFieldsFromMetadata($entityMetadata[0]);

            // confirm
            $dialog->writeSection($output, 'Generating code for '. $bundle->getName());
                           
            //Generate Bundle/Entity files
            $avroEntityGenerator = new AvroEntityGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);    
            $avroEntityGenerator->setUpdateDb(false);
            $avroEntityGenerator->generate();  
            
            //Generate Controller file
            $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroControllerGenerator->setRoutingFormat($routingFormat);
            $avroControllerGenerator->generate();

            //Generate View files
            $avroViewGenerator = new AvroViewGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroViewGenerator->generate();    
            
            if ($style == 'knockout') {
                //Generate Knockout model files
                $avroKnockoutModelGenerator = new AvroKnockoutModelGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
                $avroKnockoutModelGenerator->generate();  
            }
            
            //Generate Form files
            $avroFormGenerator = new AvroFormGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroFormGenerator->generate();

            //Generate importer
            $avroImportHandlerGenerator = new AvroImportHandlerGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroImportHandlerGenerator->generate();  
            
            //Generate Feature files
            $avroFeatureGenerator = new AvroFeatureGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroFeatureGenerator->generate();

            //Update services.yml
            $avroServicesGenerator = new AvroServicesGenerator($container, $dialog, $output, $bundle, $entity, $fields, $style, $overwrite);
            $avroServicesGenerator->setServiceConfigFormat($serviceConfigFormat);
            $avroServicesGenerator->generate();     
        }

        $dialog->writeSection($output, 'Everything was created succesfully!');
    }
}
