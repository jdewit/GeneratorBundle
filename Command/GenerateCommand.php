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
use Avro\GeneratorBundle\Command\Validators;
use Symfony\Component\Console\Command\Command;
use Avro\GeneratorBundle\Generator\Generator;

/*
 * Generator Command class 
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class GenerateCommand extends ContainerAwareCommand
{
    protected $input;
    protected $output;
    protected $dialog;
    protected $container;

    protected function configure()
    {
        $this
            ->setName('avro:generate')
            ->setAliases(array('avro:generate'))
            ->setDescription('Generates code from an entity.')
            ->setHelp(<<<EOT
The <info>generate:avro:all</info> command generates code in a bundle.
EOT
        );
    }

    /**
     * Begin console command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {        
        $this->input = $input;
        $this->output = $output;
        $container = $this->getContainer();
        $this->container = $container;
        $dialog = $this->getDialogHelper();
        $this->dialog = $dialog;
        
        $dialog->writeSection($output, 'Welcome to the Avro code generator!');

        $fromEntity = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Would you like to generate code based on an entity?', 'yes', '?'), true); 

        if ($fromEntity) {
            $output->writeln(array(
                'Enter the name of the entity you wish to create code for.',
                '(ex. AvroDemoBundle:Blog)',
                '',
                'If you wish to generate code for all entities in the bundle,', 
                'enter just the bundle name. (ex. AvroDemoBundle)'
            ));

            $input = $dialog->ask($output, $dialog->getQuestion('Bundle name with or without entity name', '', ':'));
        } else {
            $input = $dialog->ask($output, $dialog->getQuestion('Enter the bundle name', '', ':'));
        }

        list($bundleName, $entities) = $this->parseShortcutNotation($input);

        $bundleExists = true;
        try {
            $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        } catch (\Exception $e) {
            $bundleExists = false;
        }

        $tag = $dialog->ask($output, $dialog->getQuestion(
'Enter the tag for the files you wish to generate.
Or just press <enter> to generate all files.'
        , '', ':'));

        if ($bundleExists) {
            if ($fromEntity) {
                $this->generateCodeFromEntities($bundle, $entities, $tag);
            } else {
                $this->generateStandaloneFiles($bundle, $tag);
            }
        } else {
            $this->generateBundle($bundleName, $tag);
        }

        $dialog->writeSection($output, 'Code generation successful!');
    }

    /*
     * Generate code for an array of entities
     *
     * @param $bundle The bundle to generate code for
     * @param array $entities array of entities to base code from
     * @param string $tag The tag for the files you wish to generate
     */
    public function generateCodeFromEntities($bundle, $entities, $tag) 
    {
        $entitiesArray = array();
        foreach ($entities as $entity) {
            $arr = array();
            if (file_exists($bundle->getPath().'/Entity/'.str_replace('\\', '/', $entity).'.php')) {
                $entityClass = $this->getContainer()->get('doctrine')->getEntityNamespace($bundle->getName()).'\\'.$entity;
                $metadata = $this->getEntityMetadata($entityClass);
                $arr['name'] = $entity;
                $arr['fields'] = $this->getFieldsFromMetadata($metadata[0]);
            }    

            $entitiesArray[] = $arr;
        }

        foreach($entitiesArray as $entity) {
            $fields = $entity['fields'];
            $entity = $entity['name'];

            // add fields
            if ($this->container->getParameter('avro_generator.add_fields')) {
                $fields = $this->fieldGenerator($entity, $fields);      
            }

            // confirm
            $this->dialog->writeSection($this->output, sprintf('Generating code for %s.', $entity));

            //Generate Bundle/Entity files
            $avroGenerator = new Generator($this->container, $this->output);    
            $avroGenerator->initializeBundleParameters($bundle->getName());
            $avroGenerator->initializeEntityParameters($entity, $fields);

            $files = $this->container->getParameter('avro_generator.files');

            if (is_array($files)) {
                foreach($files as $file) {
                    if ($tag) {
                        if (in_array($tag, $file['tags'])) {
                            $avroGenerator->generate($file);  
                        }
                    } else {
                        $avroGenerator->generate($file);  
                    }
                }
            }
        }
    }

    /*
     * Generate Standalone Files
     *
     * @param $bundle
     * @param $tag
     */
    public function generateStandaloneFiles($bundle, $tag) 
    {
        $avroGenerator = new Generator($this->container, $this->output);    
        $avroGenerator->initializeBundleParameters($bundle->getName());

        $standaloneFiles = $this->container->getParameter('avro_generator.standalone_files');
        if (is_array($standaloneFiles)) {
            foreach($standaloneFiles as $file) {
                if ($tag) {
                    if (in_array($tag, $file['tags'])) {
                        $avroGenerator->generate($file);  
                    }
                } else {
                    $avroGenerator->generate($file);  
                }
            }
        }
    }

    /*
     * Parse shortcut notation
     *
     * @param string $shortcut
     * @return array($bundleName, $entities)
     */
    protected function parseShortcutNotation($shortcut)
    {
        if (false === $pos = strpos($shortcut, ':')) {

            $bundleName = Validators::validateBundleName($shortcut);
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
            $entity = str_replace('/', '\\', $shortcut);
            $bundleName = Validators::validateBundleName(substr($entity, 0, $pos));
            $entities = array(substr($entity, $pos + 1));
        }

        return array($bundleName, $entities);
    }

    /*
     * Get Entity Metadata
     *
     * @param $entity
     * @return array Metadata
     */
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
     * @param $entity
     * @param $oldFields
     * @return array $fields
     */
    protected function fieldGenerator($entity, $oldFields) {
        // fields
        $fields = array();

        $this->dialog->writeSection($this->output, 'Add some fields to your '.$entity.' entity');
        $this->output->write('<info>Available types:</info> ');

        $types = array_keys(Type::getTypesMap());
        $types[] = "manyToOne";
        $types[] = "manyToMany";
        $types[] = "oneToMany";
        $types[] = "oneToOne";
        $count = 20;
        foreach ($types as $i => $type) {
            if ($count > 50) {
                $count = 0;
                $this->output->writeln('');
            }
            $count += strlen($type);
            $this->output->write(sprintf('<comment>%s</comment>', $type));
            if (count($types) != $i + 1) {
                $this->output->write(', ');
            } else {
                $this->output->write('.');
            }
        }
        $this->output->writeln('');

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
            $this->output->writeln('');
            $data['fieldName'] = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('New field name (press <return> to stop adding fields)', null), function ($name) use ($fields) {
                if (isset($fields[$name]) || 'id' == $name) {
                    throw new \InvalidArgumentException(sprintf('Field "%s" is already defined.', $name));
                }

                return $name;
            });

            if (empty($data['fieldName'])) {
                break;
            }

            $type = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Field type', 'string'), $fieldValidator, false, 'string');
            $data['type'] = $type;

            if ($type == "decimal") {
                $data['precision'] = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Field precision', 10), $lengthValidator, false, 10);
                $data['scale'] = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Field scale', 2), $lengthValidator, false, 2);
            }
            if ($type == "oneToOne") {
                $data['targetEntity'] = $entity;
            }
            if ($type == "manyToOne" || $type == "oneToMany" || $type == "manyToMany") {
                $data['targetEntity'] = $this->dialog->ask($this->output, 'Enter the target entity (ie. Acme\TestBundle\Entity\Post): ');  
                $data['orphanRemoval'] = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Orphan removal?', 'no', '?'), false); 
                if ($type == 'oneToMany' || $type == 'manyToMany') {
                    $bidirectional = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Is this a bi-directional mapping?', 'no', '?'), false); 
                    $cascade = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Cascade all for this mapping?', 'no', '?'), false); 
                    if ($cascade) {
                        $data['cascade'][] = 'all';
                    } else {
                        $data['cascade'] = array();
                    }
                    if ($bidirectional) {
                        $data['isOwningSide'] = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Is this the owning side?', 'yes', '?'), true); 
                        if ($data['isOwningSide']) {
                            $data['mappedBy'] = $this->dialog->ask($this->output, 'Enter mappedBy: (ie. post): '); 
                            $data['inversedBy'] = false;
                        } else {
                            $data['inversedBy'] = $this->dialog->ask($this->output, 'Enter inversedBy: (ie. tags): ');  
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
                $data['length'] = $this->dialog->askAndValidate($this->output, $this->dialog->getQuestion('Field length', 255), $lengthValidator, false, 255);
            }

            if ($type == 'oneToOne' || $type == 'manyToOne' || $type == 'manyToMany') {
                $data['nullable'] = false;
            } else {
                $data['nullable'] = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('nullable?: ', 'yes', '?'), true); 
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
            $this->output->writeln('<error>No fields were provided</error>');

            return 1;
        }
        
        return $fields;
    }

    /*
     * Create a new bundle
     *
     * @param string $bundleName The bundle name
     * @param string $tag The tag for the files you wish to generate
     */
    public function generateBundle($bundleName, $tag) 
    {
        $filesystem = $this->container->get('filesystem');

        $this->dialog->writeSection($this->output, sprintf('%s does not exist. Would you like to create it?', $bundleName));

        if (!$this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Create a new bundle?', 'yes', '?'), true)) {
            return false;
        }

        if (Validators::validateBundleName($bundleName)) {
            list($vendor, $basename) = $this->parseBundleName($bundleName);
        } else {
            $this->dialog->writeSection($this->output, 'Welcome to the Avro bundle generator');

            // namespace
            $this->output->writeln(array(
                '',
                'Enter the vendor name for the bundle. (ie. Application, FOS, Sensio, etc)'
            ));

            // vendor
            $vendor = $this->dialog->ask($this->output, 'Bundle vendor: ');  
            $vendor = Validators::validateVendor($vendor);

            // bundle name
            $this->output->writeln(array(
                '',
                'Enter the base name of the bundle. (ie. UserBundle, CalendarBundle, etc)'
            ));
            $basename = $this->dialog->ask($this->output, 'Bundle base name: ');  
            $bundleName = Validators::validateBundleName($vendor.$basename);
        }

        //bundleNamespace
        $bundleNamespace = Validators::validateBundleNamespace($vendor.'\\'.$basename);

        $bundlePath = $this->getContainer()->getParameter('kernel.root_dir').'/../vendor/'.lcfirst($vendor).'/'.strtolower(str_replace('Bundle', '', $basename).'-bundle').'/'.$vendor.'/'.$basename.'/';       

        $generator = new Generator($this->container, $this->output);    
        $generator->initializeBundleParameters($vendor.$basename);

        $folders = $this->container->getParameter('avro_generator.bundle_folders');
        if (is_array($folders)) {
            foreach($folders as $folder) {
                if ($tag && array_key_exists('tags', $folder)) {
                    if (in_array($tag, $folder['tags'])) {
                        $generator->renderFolder($bundlePath.$folder['path']);
                    } 
                } else {
                    $generator->renderFolder($bundlePath.$folder['path']);
                }
            }
        }

        $files = $this->container->getParameter('avro_generator.bundle_files');
        if (is_array($files)) {
            foreach($files as $file) {
                if ($tag && array_key_exists('tags', $file)) {
                    if (in_array($tag, $file['tags'])) {
                        $generator->renderFile($file['template'], $file['filename']);  
                    }
                } else {
                    $generator->renderFile($file['template'], $file['filename']);  
                }
            }
        }

    }

    /*
     * Parse bundle name
     *
     * @param string $bundleName The bundles name
     * @return array 
     */
    public function parseBundleName($bundleName)
    {
        $arr = preg_split('/(?<=[a-z])(?=[A-Z])/x',$bundleName);

        return array($arr[0], $arr[1].$arr[2]);
    }
}
