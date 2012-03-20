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

use Doctrine\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\DBAL\Types\Type;
use Avro\GeneratorBundle\Command\GenerateDoctrineCommand;
use Avro\GeneratorBundle\Command\Validators;

/*
 * Base Generator Command class 
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
abstract class GenerateAvroCommand extends ContainerAwareCommand
{

    /*
     * Base command function
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return array 
     */
    protected function baseCommand(InputInterface $input, OutputInterface $output, $dialog) { 
        $output->writeln(array('Enter the name of the entity you wish to create code for. (ie. AcmeTestBundle:Blog',
            '',
            'If you wish to generate code for all of the entities in the bundle, provide only the bundle name. (ie. AcmeTestBundle'
        ));

        $input = $dialog->ask($output, $dialog->getQuestion('Bundle name with or without entity name', '', ':'));

        if (false === $pos = strpos($input, ':')) {
            $bundleName = $input;
            $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $cmf = $this->em->getMetadataFactory();
            $metadatas = $cmf->getAllMetadata();
            $entities = array();
            foreach($metadatas as $metadata) {
                $entityNamespaceArray = explode("\\", $metadata->getName()); 
                $vendor = $entityNamespaceArray[0];
                $bn = $vendor.$entityNamespaceArray[1];
                if ($bn === $bundleName) {
                    $entities[] = $entityNamespaceArray[3];
                }
            }
        } else {
            list($bundleName, $entities) = $this->parseShortcutNotation($input);
        }

        try {
            $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(sprintf('Bundle "%s" does not exist.', $bundleName));
        }

        $dialog->writeSection($output, array('This entity exists. Would you like to overwrite existing code?',
            'If not, enter false and generated code will be written in a temp folder in your bundle.'
        ));
        $overwrite = $dialog->askConfirmation($output, $dialog->getQuestion('Overwrite existing code', 'no', '?'), false);
        $style = $dialog->askAndValidate($output, $dialog->getQuestion('Enter code style you would like to generate. (1. default, 2. knockout)', '1. default', '?'), array('Avro\GeneratorBundle\Command\Validators', 'validateStyle'), '2'); 

        $entitiesArray = array();
        foreach ($entities as $entity) {
            $arr = array();
            if (file_exists($bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundleName).'\\'.$entity;
                $metadata = $this->getEntityMetadata($entityClass);
                $arr['name'] = $entity;
                $arr['fields'] = $this->getFieldsFromMetadata($metadata[0]);
            }    

            $entitiesArray[] = $arr;
        }
    

        return array($bundle, $entitiesArray, $style, $overwrite);
    }

    /*
     * Parse shortcut notation
     *
     * @param string $shortcut
     * @return array
     */
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), array(substr($entity, $pos + 1)));
    }

    protected function getEntityMetadata($entity)
    {
        $factory = new MetadataFactory($this->getContainer()->get('doctrine'));

        return $factory->getClassMetadata($entity)->getMetadata();
    }

    /*
     * Get fields from metadata
     *
     * @param ClassMetadataInfo $metadata
     * @return array $fields
     */
    protected function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldMappings = $metadata->fieldMappings;
        foreach ($fieldMappings as $mapping) {
            $fieldMappings[$mapping['fieldName']]['nullable'] = true;
        }
        $associationMappings = $metadata->associationMappings;
        foreach ($associationMappings as $mapping) {
            // convert association type from integer to text
            switch ($mapping['type']) {
                case "1":
                    $associationMappings[$mapping['fieldName']]['type'] = 'oneToOne';
                break;
                case "2":
                    $associationMappings[$mapping['fieldName']]['type'] = 'manyToOne'; 
                break;
                case "4":
                    $associationMappings[$mapping['fieldName']]['type'] = 'oneToMany'; 
                break;
                case "8":
                    $associationMappings[$mapping['fieldName']]['type'] = 'manyToMany';
                break;
            }
        }
        $fields = array_merge($fieldMappings, $associationMappings);
       //print_r($fields); exit;
        //Remove manually managed fields
        unset($fields['id']);
        unset($fields['legacyId']);
        unset($fields['owner']);
        unset($fields['createdAt']);
        unset($fields['updatedAt']);
        unset($fields['isDeleted']);
        unset($fields['deletedAt']);

        return $fields;
    }    
    
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Avro\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }    

    /*
     * Field Generator 
     * Prompt user to add more fields
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $dialog
     * @param $entity
     * @param $oldFields
     * @return array $fields
     */
    protected function fieldGenerator($input, $output, $dialog, $entity, $oldFields) {
        // fields
        $fields = $input->getOption('fields');

        $dialog->writeSection($output, 'Add some fields to your '.$entity.' entity');
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

            if ($type == "decimal") {
                $data['precision'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field precision', 10), $lengthValidator, false, 10);
                $data['scale'] = $dialog->askAndValidate($output, $dialog->getQuestion('Field scale', 2), $lengthValidator, false, 2);
            }
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

            if ($type == 'oneToOne' || $type == 'manyToOne' || $type == 'manyToMany') {
                $data['nullable'] = false;
            } else {
                $data['nullable'] = $dialog->askConfirmation($output, $dialog->getQuestion('nullable?: ', 'yes', '?'), true); 
            }

            $fields[$data['fieldName']] = $data;
        }
  
        if(false !== $oldFields) {
            if (!empty($fields)) { 
                $fields = array_merge_recursive($oldFields, $fields);
            } else {
                $fields = $oldFields;
            }
        }  

        if (!is_array($fields)) {
            $output->writeln('<error>No fields were provided</error>');

            return 1;
        }
        
        return array($fields);
    }

}
