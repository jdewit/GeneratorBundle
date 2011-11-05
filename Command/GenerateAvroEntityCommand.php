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
        $fields = $this->addFields($input, $output, $dialog);      

        if(false !== $oldFields) {
            $fields = array_merge_recursive($oldFields, $fields);
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
        $avroEntityGenerator = new AvroEntityGenerator($container, $output, $bundle);    
        $avroEntityGenerator->generate($entity, $fields, $writeManager);  
        
        $dialog->writeSection($output, $entity.' entity generated succesfully!');
    }

    private function addFields(InputInterface $input, OutputInterface $output, DialogHelper $dialog)
    {
        $fields = $input->getOption('fields');
        $output->writeln(array(
            '',
            'Instead of starting with a blank entity, you can add some fields now.',
            'Note that the primary key will be added automatically (named <comment>id</comment>).',
            '',
        ));
        $output->write('<info>Available types:</info> ');

        $types = array_keys(Type::getTypesMap());
        $types[] = "manyToOne";
        $types[] = "manyToMany";
        $types[] = "oneToMany";
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
            $name = $dialog->askAndValidate($output, $dialog->getQuestion('New field name (press <return> to stop adding fields)', null), function ($name) use ($fields) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" is already defined.', $name));
                }

                return $name;
            });
            if (!$name) {
                break;
            }

            $defaultType = 'string';

            if (substr($name, -3) == '_at') {
                $defaultType = 'datetime';
            } else if (substr($name, -3) == '_id') {
                $defaultType = 'integer';
            }

            $type = $dialog->askAndValidate($output, $dialog->getQuestion('Field type', $defaultType), $fieldValidator, false, $defaultType);

            $data = array('fieldName' => $name, 'type' => $type);
            
            if ($type == "manyToOne" || $type == "oneToMany" || $type == "manyToMany") {
                $targetEntity = $dialog->ask($output, 'Enter the target entity: (ie. Acme\TestBundle\Entity\Post)');            
                $data['targetEntity'] = $targetEntity;
            }
            if ($type == "oneToMany" || $type == "manyToMany") {
                $mappedBy = $dialog->ask($output, 'mappedBy: (ie. post)');            
                $data['mappedBy'] = $mappedBy;
            }            
            if ($type == "oneToMany" || $type == "manyToMany") {
                $cascade = $dialog->ask($output, 'cascade?: (ie. persist)', 'persist');            
                $data['cascade'] = $cascade;
            }
            if ($type == "oneToMany" || $type == "manyToMany") {
                $orphanRemoval = $dialog->ask($output, 'orphan removal?: (ie. true)', 'true');            
                $data['orphanRemoval'] = $orphanRemoval;
            }            

            if ($type == 'string') {
                $data['length'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field length', 255), $lengthValidator, false, 255);
            }

            $fields[$name] = $data;
        }

        return $fields;
    }
 
}
