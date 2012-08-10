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

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Avro\GeneratorBundle\Twig\GeneratorExtension;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/*
 * Generator Command class
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class GenerateCommand extends ContainerAwareCommand
{
    protected $registry;
    protected $input;
    protected $output;
    protected $dialog;
    protected $container;
    protected $dbDriver;
    protected $manager;
    protected $filesystem;
    protected $parameters = array();
    protected $bundleFolder;
    protected $bundleDir;
    protected $bundleVendor;
    protected $bundleBaseName;
    protected $bundlePath;
    protected $bundleNamespace;
    protected $bundleAlias;
    protected $bundleAliasCC;
    protected $bundleCoreName;

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
        $this->container = $this->getContainer();
        $this->dialog = $this->getDialogHelper();
        $this->dbDriver = $this->container->getParameter('avro_generator.db_driver');
        $this->bundleFolder = $this->container->getParameter('avro_generator.bundle_folder');
        $this->registry = $this->container->get('doctrine');
        $this->filesystem = $this->container->get('filesystem');

        foreach ($this->container->getParameterBag()->all() as $k => $v) {
            $pos = strpos($k, '.');
            $alias = substr($k, 0, $pos);
            $parameter = substr($k, $pos + 1);

            $this->parameters[$alias][$parameter] = $v;
        }

        switch($this->dbDriver) {
            case 'orm':
                $this->manager = $this->container->get('doctrine.orm.entity_manager');
            break;
            case 'mongodb':
                $this->manager = $this->container->get('doctrine.odm.mongodb.document_manager');
            break;
        }

        $this->dialog->writeSection($this->output, 'Welcome to the Avro code generator!');

        $fromEntity = $this->dialog->askConfirmation($this->output, $this->dialog->getQuestion('Would you like to generate code based on an entity/document?', 'yes', '?'), true);

        if ($fromEntity) {
            $this->output->writeln(array(
                'Enter the name of the entity you wish to create code for.',
                '(ex. AvroDemoBundle:Blog)',
                '',
                'If you wish to generate code for all entities in the bundle,',
                'enter just the bundle name. (ex. AvroDemoBundle)'
            ));

            $input = $this->dialog->ask($this->output, $this->dialog->getQuestion('Bundle name with or without entity name', '', ':'));
        } else {
            $input = $this->dialog->ask($this->output, $this->dialog->getQuestion('Enter the bundle name', '', ':'));
        }

        list($bundleName, $entities) = $this->parseShortcutNotation($input);

        $this->setBundleParameters($bundleName);

        $bundleExists = true;
        try {
            $bundle = $this->getContainer()->get('kernel')->getBundle($bundleName);
        } catch (\Exception $e) {
            $bundleExists = false;
        }

        $tag = $this->dialog->ask($this->output, $this->dialog->getQuestion(
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

        $this->dialog->writeSection($this->output, 'Code generation successful!');
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
            switch($this->dbDriver) {
                case 'orm':
                    $entityPath = $this->bundlePath.'/Entity/'.str_replace('\\', '/', $entity).'.php';
                    $entityNamespace = $this->bundleNamespace.'\\Entity\\'.str_replace('/', '\\', $entity);
                break;
                case 'mongodb':
                    $entityPath = $this->bundlePath.'/Document/'.str_replace('\\', '/', $entity).'.php';
                    $entityNamespace = $this->bundleNamespace.'\\Document\\'.str_replace('/', '\\', $entity);
                break;
            }

            if (file_exists($entityPath)) {
                $metadata = $this->getEntityMetadata($entityNamespace);
                $arr['name'] = $entity;
                $arr['fields'] = $this->getFieldsFromMetadata($metadata);
            } else {
                $arr['name'] = $entity;
                $arr['fields'] = array();
            }

            $entitiesArray[] = $arr;
        }
        foreach($entitiesArray as $entity) {
            $fields = $entity['fields'];
            $entity = $entity['name'];

            $this->setEntityParameters($entity, $fields);

            // add fields
            if ($this->container->getParameter('avro_generator.add_fields')) {
                switch($this->dbDriver) {
                    case 'orm':
                        $fields = $this->fieldGenerator($entity, $fields);
                    break;
                    case 'mongodb':
                        //TODO:
                    break;
                }
            }

            // confirm
            $this->dialog->writeSection($this->output, sprintf('Generating code for %s.', $entity));

            $files = $this->container->getParameter('avro_generator.files');

            if (is_array($files)) {
                foreach($files as $file) {
                    if ($tag) {
                        if (in_array($tag, $file['tags'])) {
                            $this->generate($file);
                        }
                    } else {
                        $this->generate($file);
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

            $cmf = $this->manager->getMetadataFactory();
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
        $cmf = $this->manager->getMetadataFactory();

        return $cmf->getMetadataFor($entity);
    }

    /*
     * Get fields from metadata
     *
     * @param $metadata
     * @return array $fields
     */
    protected function getFieldsFromMetadata($metadata)
    {
        $fieldMappings = $metadata->fieldMappings;
        foreach ($fieldMappings as $mapping) {
            $fieldMappings[$mapping['fieldName']]['nullable'] = true;
        }
        if ($this->dbDriver == 'orm') {
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
        } else {
            $fields = $fieldMappings;
        }
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

        $folders = $this->container->getParameter('avro_generator.bundle_folders');
        if (is_array($folders)) {
            foreach($folders as $folder) {
                if ($tag && array_key_exists('tags', $folder)) {
                    if (in_array($tag, $folder['tags'])) {
                        $this->renderFolder($bundlePath.$folder['path']);
                    }
                } else {
                    $this->renderFolder($bundlePath.$folder['path']);
                }
            }
        }

        $files = $this->container->getParameter('avro_generator.bundle_files');
        if (is_array($files)) {
            foreach($files as $file) {
                if ($tag && array_key_exists('tags', $file)) {
                    if (in_array($tag, $file['tags'])) {
                        $this->renderFile($file['template'], $file['filename']);
                    }
                } else {
                    $this->renderFile($file['template'], $file['filename']);
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

    /*
     * Set Parameters
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /*
     * Set bundle parameters
     *
     * @param string $bundleName
     */
    public function setBundleParameters($bundleName)
    {
        $this->bundleName = $bundleName;

        $arr = preg_split('/(?<=[a-z])(?=[A-Z])/x',$bundleName);

        $this->bundleVendor = array_shift($arr);
        $this->bundleBaseName = implode("", $arr);
        if ($this->bundleFolder == 'src') {
            $this->bundlePath = $this->container->getParameter('kernel.root_dir').'/../src/'.$this->bundleVendor.'/'.$this->bundleBaseName.'/';
        } else {
            $this->bundlePath = $this->container->getParameter('kernel.root_dir').'/../vendor/'.lcfirst($this->bundleVendor).'/'.strtolower(str_replace('Bundle', '', $this->bundleBaseName).'-bundle').'/'.$this->bundleVendor.'/'.$this->bundleBaseName.'/';
        }
        $this->bundleNamespace = $this->bundleVendor.'\\'.$this->bundleBaseName;
        $this->bundleAlias = strtolower($this->bundleVendor.'_'.str_replace('Bundle', '', $this->bundleBaseName));
        $this->bundleAliasCC = $this->bundleVendor.str_replace('Bundle', '', $this->bundleBaseName);
        $this->bundleCoreName = str_replace(strtolower($this->bundleVendor).'_','',$this->bundleAlias);

        $bundleParameters = array(
            'bundleVendor' => $this->bundleVendor,
            'bundleBaseName' => $this->bundleBaseName,
            'bundleName' => $this->bundleName,
            'bundleCoreName' => $this->bundleCoreName,
            'bundlePath' => $this->bundlePath,
            'bundleNamespace' => $this->bundleNamespace,
            'bundleAlias' => $this->bundleAlias,
        );

        $this->parameters = array_merge($this->parameters, $bundleParameters);
    }

    /*
     * Set entity parameters
     *
     * @param string $entity The entity name
     * @param array $fields Array of the entities fields
     */
    public function setEntityParameters($entity, $fields)
    {
        $parameters = array(
            'entity' => $entity,
            'entityCC' => $this->toCamelCase($entity),
            'entityUS' => $this->toUnderscore($entity),
            'entityTitle' => $this->toTitle($entity),
            'entityTitleLC' => strtolower($this->toTitle($entity)),
            'fields' => $this->customizeFields($fields),
            'uniqueManyToOneRelations' => $this->uniqueManyToOneRelations($this->customizeFields($fields)),
        );

        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Generates a file if it does not exist.
     *
     * @param $file
     */
    public function generate($file)
    {
        $filename = $file['filename'];
        $template = $file['template'];

        // Add user defined parameters
        if (array_key_exists('parameters', $file)) {
            foreach($file['parameters'] as $k => $v) {
                $this->parameters[$k] = $v;
            }
        }

        // change filename if overwrite is true
        if (true === $this->container->getParameter('avro_generator.overwrite')) {
            $this->renderFile($template, $filename);
        } else {
            $filename1 = 'Temp/split/'.$this->parameters['entity'].'/'.$filename;
            $filename2 = 'Temp/src/'.$filename;

            $this->renderFile($template, $filename1);
            $this->renderFile($template, $filename2);
        }

        $this->executeManipulators($file);
    }

    /*
     * Execute code manipulators
     */
    public function executeManipulators($file)
    {
        $manipulator = array_key_exists('manipulator', $file) ? $file['manipulator'] : false;
        if ($manipulator && $this->parameters['avro_generator']['overwrite']) {
            $manipulatorService = $this->container->get($manipulator['service']);
            $manipulatorService->setParameters($this->parameters);
            $manipulatorService->setFilename($manipulator['filename']);
            $manipulatorService->setRootDir($this->container->get('kernel')->getRootDir().'/..');
            $manipulatorService->setBundleDir($this->parameters['bundlePath']);
            foreach($manipulator['setters'] as $k => $v) {
                $manipulatorService->{'set'.ucFirst($k)}($v);
            }
            $manipulatorService->{$manipulator['method'] ? $manipulator['method'] : 'manipulate'}();
        }
    }

    /*
     * Renders a new file
     *
     * @param $template The file to use as a template
     * @param $filename The location of the new file
     */
    public function renderFile($template, $filename)
    {
        if (strpos($template, ':')) {
            $arr = explode(":", $template);
            $template = $this->container->get('kernel')->getBundle($arr[0])->getPath().'/'.$arr[1];
        }

        $filename = $this->parameters['bundlePath'].$filename;

        // replace any placeholders in the filename
        $filename = str_replace(
            array(
                '{{ entity }}',
                '{{ entityCC }}',
                '{{ bundleVendor }}',
                '{{ bundleName }}',
                '{{ bundleCoreName }}'
            ), array(
                array_key_exists('entity', $this->parameters) ? $this->parameters['entity'] : '',
                array_key_exists('entityCC', $this->parameters) ? $this->parameters['entityCC'] : '',
                array_key_exists('bundleVendor', $this->parameters) ? $this->parameters['bundleVendor'] : '',
                array_key_exists('bundleName', $this->parameters) ? $this->parameters['bundleName'] : '',
                array_key_exists('bundleCoreName', $this->parameters) ? ucFirst($this->parameters['bundleCoreName']) : ''
            ),
            $filename
        );

        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $this->output->write(sprintf('Generating %s: ', str_replace($this->parameters['bundlePath'], "", $filename)));

        try {
            $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array(__DIR__.'/../Templates', '/', $this->container->get('kernel')->getRootDir())), array(
                'debug'            => true,
                'cache'            => false,
                'strict_variables' => true,
                'autoescape'       => false,
            ));
            $twig->addExtension(new GeneratorExtension());

            file_put_contents($filename, $twig->render($template, $this->parameters));

            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }
    }

    /*
     * Renders a new folder
     *
     * @param $path The path of the new folder
     */
    public function renderFolder($path)
    {
        $this->output->write('Creating '.$path.': ');

        try {
            $this->filesystem->mkdir($path);
            $this->output->writeln('<info>Ok</info>');
        } catch (\RuntimeException $e) {
            $this->output->writeln(array(
                '<error>Fail</error>',
                $e->getMessage(),
                ''
            ));
        }
    }

    /*
     * Run a console command
     *
     * @param string $command Command
     * @param array $options Command options
     */
    public function runConsole($command, Array $options = array())
    {
        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);
        if (empty($options["-e"])) {
            $options["-e"] = "dev";
        }
        $options = array_merge($options, array('command' => $command));
        $application->run(new ArrayInput($options));
    }

    /**
    * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
    *
    * @param  string $str String in camel case format
    * @return string $str Translated into underscore format
    */
    public function toUnderscore($str) {
        $str[0] = strtolower($str[0]);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');

        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
    * Translates a camel case string into a string with underscores (e.g. firstName -&gt; first_name)
    *
    * @param string $str String in camel case format
    * @return string $str Translated into underscore format
    */
    public function toTitle($str) {
        if (is_array($str)) {
            $str = $str[0];
        }
        $str = ucfirst($str);
        $func = create_function('$c', 'return " " . ucfirst($c[1]);');

        return preg_replace_callback('/([A-Z])/', $func, $str);
    }

    /**
    * Translates a string with underscores into camel case (e.g. first_name -&gt; firstName)
    *
    * @param string $str String in underscore format
    * @return string $str translated into camel caps
    */
    public function toCamelCase($str) {
        $str = lcfirst($str);
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /*
     * Add custom attributes to fields
     *
     * @param array $fields
     * @return array $customizedFields
     */
    public function customizeFields($fields)
    {
        $customFields = array();
        foreach ($fields as $field) {
            $field['fieldTitle'] = $this->toTitle($field['fieldName']);
            if ($field['type'] == 'manyToOne' || $field['type'] == 'oneToMany' || $field['type'] == 'manyToMany') {
                $targetEntity = $field['targetEntity'];
                $arr = explode('\\', $targetEntity);
                $field['targetVendor'] = $arr[0];
                $field['targetBundle'] = $arr[1];
                $field['targetBundleAlias'] = strtolower($arr[0].'_'.str_replace('Bundle', '', $arr[1]));
                $field['targetEntityName'] = lcfirst($arr[3]);
            }
            $customFields[] = $field;
        }

        return $customFields;
    }

    /*
     * Returns an array of the entities unique manyToOne relations
     *
     * @param array $fields
     * @return array $uniqueManyToOneRelations
     */
    public function uniqueManyToOneRelations($fields)
    {
        $relations = array();
        $result = array();

        foreach($fields as $field) {
            $type = $field['type'];
            if ($type == 'manyToOne' || $type == 'oneToMany' || $type == 'manyToMany') {
                $target = $field['targetEntity'];
                if (!in_array($target, $relations) && $target != 'Avro\AssetBundle\Entity\Image') {
                    $relations[] = $target;
                    $result[] = $field;
                }
            }
        }

        return $result;
    }

}
