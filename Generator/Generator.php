<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Generator;

use Avro\GeneratorBundle\Twig\GeneratorExtension;

use Avro\CaseBundle\Util\CaseConverter;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * Generator is the base class for all generators.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Generator
{
    public $container;
    public $registry;
    public $filesystem;
    public $output;
    public $parameters = array();

    /**
     * Constructor
     *
     * @param Container $container The container object
     * @param Output    $output    The console output
     */
    public function __construct($container, $output)
    {
        $this->container = $container;
        $this->converter = new CaseConverter();
        $this->registry = $container->get('doctrine');
        $this->filesystem = $container->get('filesystem');
        $this->output = $output;

        $parameters = $container->getParameterBag()->all();
        foreach ($parameters as $k => $v) {
            $pos = strpos($k, '.');
            $alias = substr($k, 0, $pos);
            $parameter = substr($k, $pos + 1);

            $this->parameters[$alias][$parameter] = $v;
        }
    }

    /**
     * Set Parameters
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Initialize bundle parameters
     *
     * @param string $bundleName The bundle name
     */
    public function initializeBundleParameters($bundleName)
    {
        $arr = preg_split('/(?<=[a-z])(?=[A-Z])/x', $bundleName);

        $bundleVendor = array_shift($arr);
        $bundleBaseName = implode("", $arr);

        $bundlePath = $this->container->getParameter('kernel.root_dir').'/../vendor/'.lcfirst($bundleVendor).'/'.strtolower(str_replace('Bundle', '', $bundleBaseName).'-bundle').'/'.$bundleVendor.'/'.$bundleBaseName.'/';
        $bundleNamespace = $bundleVendor.'\\'.$bundleBaseName;
        $bundleAlias = strtolower($bundleVendor.'_'.str_replace('Bundle', '', $bundleBaseName));
        $bundleAliasCC = $bundleVendor.str_replace('Bundle', '', $bundleBaseName);
        $bundleCoreName = str_replace(strtolower($bundleVendor).'_', '', $bundleAlias);

        $parameters = array(
            'bundleVendor' => $bundleVendor,
            'bundleBaseName' => $bundleBaseName,
            'bundleName' => $bundleName,
            'bundleCoreName' => $bundleCoreName,
            'bundlePath' => $bundlePath,
            'bundleNamespace' => $bundleNamespace,
            'bundleAlias' => $bundleAlias,
        );

        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Initialize parameters
     *
     * @param string $entity The entity name
     * @param array  $fields Array of the entities fields
     */
    public function initializeEntityParameters($entity, $fields)
    {
        $parameters = array(
            'entity' => $entity,
            'entityCC' => $this->converter->toCamelCaseCase($entity),
            'entityUS' => $this->converter->toUnderscoreCase($entity),
            'entityTitle' => $this->toTitleCase($entity),
            'entityTitleLC' => strtolower($this->toTitleCase($entity)),
            'fields' => $this->customizeFields($fields),
            'uniqueManyToOneRelations' => $this->uniqueManyToOneRelations($this->customizeFields($fields)),
        );

        $this->parameters = array_merge($this->parameters, $parameters);
    }

    /**
     * Generates a file if it does not exist.
     *
     * @param string $file The filename
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

    /**
     * Execute code manipulators
     *
     * @param string $file The filename
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

    /**
     * Renders a new file
     *
     * @param string $template The file to use as a template
     * @param string $filename The location of the new file
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

    /**
     * Renders a new folder
     *
     * @param string $path The path of the new folder
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

    /**
     * Run a console command
     *
     * @param string $command Command
     * @param array  $options Command options
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
     * Add custom attributes to fields
     *
     * @param array $fields
     *
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

    /**
     * Returns an array of the entities unique manyToOne relations
     *
     * @param array $fields
     *
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
