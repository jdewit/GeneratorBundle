<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Generator;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Avro\GeneratorBundle\Twig\GeneratorExtension;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * Generator is the base class for all generators.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Generator
{
    public $container;
    public $registry;
    public $filesystem;
    public $output;
    public $parameters = array();

    public function __construct($container, $output)
    {
        $this->container = $container;
        $this->registry = $container->get('doctrine');
        $this->filesystem = $container->get('filesystem');
        $this->output = $output;
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
     * Generate bundle parameters
     *
     * @param string $bundleName 
     */
    public function generateBundleParameters($bundleName)
    {
        $arr = preg_split('/(?<=[a-z])(?=[A-Z])/x',$bundleName);

        $bundleVendor = array_shift($arr);
        $bundleBasename = implode("", $arr);

        $bundlePath = $this->container->getParameter('kernel.root_dir').'/../vendor/'.lcfirst($bundleVendor).'/'.strtolower(str_replace('Bundle', '', $bundleBasename).'-bundle').'/'.$bundleVendor.'/'.$bundleBasename.'/';       
        $bundleNamespace = $bundleVendor.'\\'.$bundleBasename;
        $bundleAlias = strtolower($bundleVendor.'_'.str_replace('Bundle', '', $bundleBasename));   
        $bundleAliasCC = $bundleVendor.str_replace('Bundle', '', $bundleBasename); 
        $bundleCorename = str_replace(strtolower($bundleVendor).'_','',$bundleAlias);

        $parameters = array(
            'bundle_vendor' => $bundleVendor,
            'bundle_basename' => $bundleBasename,
            'bundle_name' => $bundleName,
            'bundle_corename' => $bundleCorename,
            'bundle_path' => $bundlePath,
            'bundle_namespace' => $bundleNamespace,  
            'bundle_alias' => $bundleAlias,          
            'db_driver' => $this->container->hasParameter($bundleAlias.'.db_driver') ? $container->getParameter($bundleAlias.'.db_driver') : 'orm',
            'style' => $this->container->getParameter('avro_generator.style'),
        );

        $this->parameters = array_merge($parameters, $this->parameters);
    }

    /*
     * Generate parameters
     *
     * @param string $entity The entity name
     * @param array $fields An arrow of the entities fields
     */
    public function generateEntityParameters($entity, $fields)
    {
        $parameters = array(
            'entity' => $entity,
            'entity_cc' => $this->toCamelCase($entity),
            'entity_us' => $this->toUnderscore($entity),
            'fields' => $this->customizeFields($fields),
            'uniqueManyToOneRelations' => $this->uniqueManyToOneRelations($this->customizeFields($fields)),
        );

        $this->parameters = array_merge($parameters, $this->parameters);
    }

    /**
     * Generates a file if it does not exist.
     */
    public function generate($file)
    {
        $filename = $file['filename'];
        $template = $file['template'];
        $manipulator = array_key_exists('manipulator', $file) ? $file['manipulator'] : false;

        // Add user defined parameters
        if (array_key_exists('parameters', $file)) {
            foreach($file['parameters'] as $k => $v) {
                $this->parameters[$k] = $v;
            }
        }
        
        // change filename if overwrite is true
        if ($this->container->getParameter('avro_generator.overwrite')) {
            if (!is_dir(dirname($filename))) {
                mkdir(dirname($filename), 0777, true);
            }
            $this->renderFile($template, $filename);
        } else {
            $newPath1= $this->bundlePath.'/Temp/split/'.$this->parameters['entity'];
            $filename1 = str_replace($this->parameters['bundle_path'], $newPath1, $filename);

            $newPath2= $this->parameters['bundle_path'].'/Temp/src';
            $filename2= str_replace($this->parameters['bundle_path'], $newPath2, $filename);

            if (!is_dir(dirname($filename1))) {
                mkdir(dirname($filename1), 0777, true);
            }

            if (!is_dir(dirname($filename2))) {
                mkdir(dirname($filename2), 0777, true);
            }

            $this->renderFile($template, $filename1);
            $this->renderFile($template, $filename2);
        }

        if ($manipulator) {
            $manipulatorService = $this->container->get($manipulator['service']);
            $manipulatorService->setParameters($this->parameters);
            $manipulatorService->setFilename($manipulator['filename']);
            $manipulatorService->setRootDir($this->container->get('kernel')->getRootDir().'/..');
            $manipulatorService->setBundleDir($this->parameters['bundle_path']);
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
     * 
     */
    public function renderFile($template, $filename)
    {   
        $arr = explode(":", $template); 
        $template = $this->container->get('kernel')->getBundle($arr[0])->getPath().'/'.$arr[1];

        $filename = $this->parameters['bundle_path'].'/'.$filename;

        // replace any placeholders in the filename
        $filename = str_replace(
            array(
                '{{ entity }}', 
                '{{ entity_cc }}',
                '{{ bundle_vendor }}',
                '{{ bundle_name }}'
            ), array(
                array_key_exists('entity', $this->parameters) ? $this->parameters['entity'] : '', 
                array_key_exists('entity_cc', $this->parameters) ? $this->parameters['entity_cc'] : '',
                array_key_exists('bundle_vendor', $this->parameters) ? $this->parameters['bundle_vendor'] : '',
                array_key_exists('bundle_name', $this->parameters) ? $this->parameters['bundle_name'] : ''
            ), 
            $filename
        );

        $this->output->write('Generating '.$filename.': ');

        try {
            $skeletonDir = __DIR__.'/../Skeleton';

            $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array($skeletonDir, '/')), array(
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
     * 
     */
    public function renderFolder($path)
    {   
        $this->output->write('Generating '.$path.': ');

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
    *
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
     * Set bundles routing format
     */
    public function setRoutingFormat($format) {
        $this->routingFormat = $format;
    }

    /*
     * Set update database trigger
     */
    public function setUpdateDb($updateDb) {
        $this->updateDb = $updateDb;
    }

    /*
     * Set service configuration format
     */
    public function setServiceConfigFormat($format) {
        $this->serviceConfigFormat = $format;
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
            if ($type == 'manyToOne') {
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
