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
use Avro\GeneratorBundle\Generator\AvroFormGenerator;
use Avro\GeneratorBundle\Generator\AvroReadmeGenerator;
use Avro\GeneratorBundle\Generator\AvroFeatureGenerator;
use Avro\GeneratorBundle\Generator\AvroServicesGenerator;

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
        
        $dialog->writeSection($output, 'Welcome to the Avro create all generator!');
        $output->writeln('Enter the name of the entity you wish to create. (ie. AcmeTestBundle:Blog');
        while (true) {
            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Avro\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));

            list($bundleName, $entity) = $this->parseShortcutNotation($entity);

            try {
                $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundleName));
            }

            break;         
        }

        $oldFields = false;
        $writeManager = true;
        if (file_exists($bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
            $output->writeln($entity.' already exists.');
            if ($dialog->askConfirmation($output, $dialog->getQuestion('Merge with existing entity? ', 'yes', '?'), true)) {
                $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundleName).'\\'.$entity;
                $metadata = $this->getEntityMetadata($entityClass);
                $oldFields = $this->getFieldsFromMetadata($metadata[0]);
                //remove manually managed fields
                unset($oldFields['id']);
                unset($oldFields['createdAt']);
                unset($oldFields['updatedAt']);
            } elseif (!$dialog->askConfirmation($output, $dialog->getQuestion('Overwrite existing entity? ', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }  
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Overwrite '.$entity.'Manager', 'no', '?'), false)) {
                $writeManager = false;
            }            
        }        
        
        // fields
        $fields = $this->addFields($input, $output, $dialog, $entity);      
        if(false !== $oldFields) {
            if (!empty($fields)) { 
                $fields = array_merge_recursive($oldFields, $fields);
            } else {
                $fields = $oldFields;
            }
        }        

        // dbDriver
        $output->writeln(array(
            '',
            'Specify the database you wish to use for your bundle.',
            '',
        ));
        $dbDriver = $dialog->askAndValidate($output, $dialog->getQuestion('Database format (orm, couchdb, mongodb)', $input->getOption('dbDriver')), array('Avro\GeneratorBundle\Command\Validators', 'validateDbDriver'), false, $input->getOption('dbDriver'));     
        
        // confirm
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate code for \"<info>%s:%s</info>\" Doctrine2 entity.", $bundleName, $entity),
            '',
        ));

        $dialog->writeSection($output, 'Generating code for '. $bundleName );
                       
        //Generate Bundle/Entity files
        $avroEntityGenerator = new AvroEntityGenerator($container, $dialog, $output, $bundle);    
        $avroEntityGenerator->generate($entity, $fields, $writeManager);  
        
        //Generate Controller file
        $avroControllerGenerator = new AvroControllerGenerator($container, $dialog, $output, $bundle);
        $avroControllerGenerator->generate($entity);

        //Generate View files
        $avroViewGenerator = new AvroViewGenerator($container, $dialog, $output, $bundle);
        $avroViewGenerator->generate($entity, $fields);    

        //Generate Form files
        $avroFormGenerator = new AvroFormGenerator($container, $dialog, $output, $bundle);
        $avroFormGenerator->generate($entity, $fields);

        //Generate Feature files
        $avroFeatureGenerator = new AvroFeatureGenerator($container, $dialog, $output, $bundle);
        $avroFeatureGenerator->generate($entity, $fields);

        //Update services.yml
        $avroServicesGenerator = new AvroServicesGenerator($container, $dialog, $output, $bundle);
        $avroServicesGenerator->generate($entity, $fields);     
    }

    private function addFields(InputInterface $input, OutputInterface $output, DialogHelper $dialog, $entity)
    {
        $fields = $input->getOption('fields');
        $output->writeln(array(
            '',
            'Add some fields to your entity',
            '',
        ));
        $output->write('<info>Available types:</info> ');

        $types = array_keys(Type::getTypesMap());
        $types[] = "manyToOne";
        $types[] = "manyToMany";
        $types[] = "oneToMany";
        $types[] = "oneToOne";
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $output->writeln('');
            }
            $count += strlen($type);
            $output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $output->write(', ');
            } else {
                $output->write('.');
            }
        }
        $output->writeln('');

        $fieldValidator = function ($type) use ($types) {
            // FIXME: take into account user-defined field types
            if (!in_array($type, $types)) {
                throw new \InvalidArgumentException(sprintf('Invalid type "%s".', $type));
            }

            return $type;
        };

        $lengthValidator = function ($length) {
            if (!$length) {
                return $length;
            }

            $result = filter_var($length, FILTER_VALIDATE_INT, array(
                'options' => array('min_range' => 1)
            ));

            if (false === $result) {
                throw new \InvalidArgumentException(sprintf('Invalid length "%s".', $length));
            }

            return $length;
        };

        while (true) {
            $output->writeln('');
            $data['fieldName'] = $dialog->askAndValidate($output, $dialog->getQuestion('New field name (press <return> to stop adding fields)', null), function ($name) use ($fields) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" is already defined.', $name));
                }

                return $name;
            });

            if (empty($data['fieldName'])) {
                break;
            }

            $type = $dialog->askAndValidate($output, $dialog->getQuestion('Field type', 'string'), $fieldValidator, false, 'string');
            $data['type'] = $type;

            if ($type == "oneToOne") {
                $data['targetEntity'] = $entity;
            }
            if ($type == "manyToOne" || $type == "oneToMany" || $type == "manyToMany") {
                $data['targetEntity'] = $dialog->ask($output, 'Enter the target entity (ie. Acme\TestBundle\Entity\Post): ');  
                $data['orphanRemoval'] = $dialog->askConfirmation($output, $dialog->getQuestion('Orphan removal?', 'no', '?'), false); 
                if ($type == 'oneToMany' || $type == 'manyToMany') {
                    $bidirectional = $dialog->askConfirmation($output, $dialog->getQuestion('Is this a bi-directional mapping?', 'no', '?'), false); 
                    $cascade = $dialog->askConfirmation($output, $dialog->getQuestion('Cascade all for this mapping?', 'no', '?'), false); 
                    if ($cascade) {
                        $data['cascade'][] = 'all';
                    } else {
                        $data['cascade'] = array();
                    }
                    if ($bidirectional) {
                        $data['isOwningSide'] = $dialog->askConfirmation($output, $dialog->getQuestion('Is this the owning side?', 'yes', '?'), true); 
                        if ($data['isOwningSide']) {
                            $data['mappedBy'] = $dialog->ask($output, 'Enter mappedBy: (ie. post): '); 
                            $data['inversedBy'] = false;
                        } else {
                            $data['inversedBy'] = $dialog->ask($output, 'Enter inversedBy: (ie. tags): ');  
                            $data['mappedBy'] = false;
                        }
                    } else {
                        $data['isOwningSide'] = false;
                        $data['mappedBy'] = false;
                        $data['inversedBy'] = false;
                    }
                }
            }             

            if ($type == 'string') {
                $data['length'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field length', 255), $lengthValidator, false, 255);
            }

            if ($type != 'oneToOne' && $type == 'manyToOne' && $type == 'manyToMany') {
                $data['nullable'] = $dialog->askConfirmation($output, $dialog->getQuestion('nullable?: ', 'yes', '?'), true); 
            } else {
                $data['nullable'] = false;
            }

            $fields[$data['fieldName']] = $data;
        }

        return $fields;
    }
 
}
