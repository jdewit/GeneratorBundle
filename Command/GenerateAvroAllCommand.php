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
use Avro\GeneratorBundle\Generator\AvroDependencyInjectionGenerator;
use Avro\GeneratorBundle\Generator\AvroReadmeGenerator;

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
        
        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }
        //get user input
        $entityInput = Validators::validateEntityName($input->getOption('entity'));
        $fields = $this->parseFields($input->getOption('fields'));

        list($bundleName, $entity) = $this->parseShortcutNotation($entityInput);
  
        $bundle = $container->get('kernel')->getBundle($bundleName);
        
        $dbDriver = $input->getOption('dbDriver');
        
        $dialog->writeSection($output, 'Generating code for '. $bundleName );
                       
        //Generate Bundle/Entity files
        $avroEntityGenerator = new AvroEntityGenerator($container, $output, $bundle);    
        $avroEntityGenerator->generate($entity, $fields);
        
        //Generate Bundle/Resources/config files
        $avroConfigGenerator = new AvroConfigGenerator($container, $output, $bundle);
        $avroConfigGenerator->generate($entity, $fields);      

        //Generate Controller file
        $avroControllerGenerator = new AvroControllerGenerator($container, $output, $bundle);
        $avroControllerGenerator->generate($entity, $fields);

        //Generate View files
        $avroViewGenerator = new AvroViewGenerator($container, $output, $bundle);
        $avroViewGenerator->generate($entity, $fields);
                
        //Generate Form files
        $avroFormGenerator = new AvroFormGenerator($container, $output, $bundle);
        $avroFormGenerator->generate($entity, $fields);

        //Generate DependencyInjection files
        $avroDependencyInjectionGenerator = new AvroDependencyInjectionGenerator($container, $output, $bundle);
        $avroDependencyInjectionGenerator->generate($entity, $fields);     
        
        $dialog->writeSection($output, $entity.' CRUD generated succesfully!');
    }
   
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Avro code generator!');

        // namespace
        $output->writeln(array(
            '',
            'Welcome to the Avro Code Generator!', 
            '',    
            'This command helps you generate Symfony2 Entity, Controller, Form, View, and Configuration code', 
            'in a bundle.',
            '',
            'First, you need to give the entity name you want to generate.',
            'You must use the shortcut notation like <comment>AcmeBlogBundle:Post</comment>.',
            'The generator will produce an abstract entity configurable for ORM or ODM,', 
            'controller, form, formHandler, views, routing, and configuration',
            ''
        ));

        while (true) {
            $entity = $dialog->askAndValidate($output, $dialog->getQuestion('The Entity shortcut name', $input->getOption('entity')), array('Sensio\Bundle\GeneratorBundle\Command\Validators', 'validateEntityName'), false, $input->getOption('entity'));

            list($bundle, $entity) = $this->parseShortcutNotation($entity);

            try {
                $b = $this->getContainer()->get('kernel')->getBundle($bundle);
            } catch (\Exception $e) {
                $output->writeln(sprintf('<bg=red>Bundle "%s" does not exist.</>', $bundle));
            }

            if (file_exists($b->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                $output->writeln($entity.' already exists.');
                if (!$dialog->askConfirmation($output, $dialog->getQuestion('Overwrite', 'yes', '?'), true)) {
                    $output->writeln('<error>Command aborted</error>');

                    return 1;
                }               
            }

            break;         
        }
        $input->setOption('entity', $bundle.':'.$entity);

        // fields
        $input->setOption('fields', $this->addFields($input, $output, $dialog));     
        
        // dbDriver
        $output->writeln(array(
            '',
            'Specify the database you wish to use for your bundle.',
            '',
        ));
        $dbDriver = $dialog->askAndValidate($output, $dialog->getQuestion('Database format (orm, couchdb, mongodb)', $input->getOption('dbDriver')), array('Avro\GeneratorBundle\Command\Validators', 'validateDbDriver'), false, $input->getOption('dbDriver'));
        $input->setOption('dbDriver', $dbDriver);        
        
        // confirm
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate code for \"<info>%s:%s</info>\" Doctrine2 entity.", $bundle, $entity),
            '',
        ));
    }

    private function parseFields($input)
    {
        if (is_array($input)) {
            return $input;
        }

        $fields = array();
        foreach (explode(' ', $input) as $value) {
            $elements = explode(':', $value);
            $name = $elements[0];
            if (strlen($name)) {
                $type = isset($elements[1]) ? $elements[1] : 'string';
                preg_match_all('/(.*)\((.*)\)/', $type, $matches);
                $type = isset($matches[1][0]) ? $matches[1][0] : $type;
                $length = isset($matches[2][0]) ? $matches[2][0] : null;

                $fields[$name] = array('fieldName' => $name, 'type' => $type, 'length' => $length);
            }
        }

        return $fields;
    }

    private function addFields(InputInterface $input, OutputInterface $output, DialogHelper $dialog)
    {
        $fields = $this->parseFields($input->getOption('fields'));
        $output->writeln(array(
            '',
            'Instead of starting with a blank entity, you can add some fields now.',
            'Note that the primary key will be added automatically (named <comment>id</comment>).',
            '',
        ));
        $output->write('<info>Available types:</info> ');

        $types = array_keys(Type::getTypesMap());
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

            if ($type == 'string') {
                $data['length'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field length', 255), $lengthValidator, false, 255);
            }

            $fields[$name] = $data;
        }

        return $fields;
    }
 
}
