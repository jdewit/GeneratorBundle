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
    protected $container;
    protected $dialog;
    protected $registry;
    protected $filesystem;
    protected $output;
    protected $parameters;
    protected $bundleAlias;
    protected $bundleAliasCC;
    protected $bundleCoreName;
    protected $bundleBasename;
    protected $bundleName;
    protected $bundleNamespace; 
    protected $bundlePath;
    protected $bundleVendor;
    protected $entity;
    protected $entityCC;
    protected $entityUS;
    protected $fields;
    protected $dbDriver;
    protected $message;
    protected $thirdParty; 
    protected $style; 
    protected $overwrite;
    protected $updateDb;
    protected $routingFormat;
    protected $serviceConfigFormat;

    public function __construct($container, $dialog, $output, $bundle = null, $entity = null, $fields = null, $style = null, $overwrite = false)
    {
        $this->container = $container;
        $this->dialog = $dialog;
        $this->registry = $container->get('doctrine');
        $this->filesystem = $container->get('filesystem');
        $this->output = $output;
        $this->style = $style;
        $this->overwrite = $overwrite;
        if ($bundle !== null) {
            $this->bundlePath = $bundle->getPath();
            if (strstr($this->bundlePath, 'vendor/bundles') == false) {
                $this->thirdParty = false;
            }
            $this->bundleNamespace = $bundle->getNamespace();   
            $this->bundleName = $bundle->getName();
            $this->bundleVendor = substr($this->bundleNamespace, 0, strpos($this->bundleNamespace, '\\'));
            $this->bundleBasename = str_replace('\\', '', substr($this->bundleNamespace, strpos($this->bundleNamespace, '\\')));
            $this->bundleAlias = strtolower($this->bundleVendor.'_'.str_replace('Bundle', '', $this->bundleBasename));   
            $this->bundleAliasCC = $this->bundleVendor.str_replace('Bundle', '', $this->bundleBasename); 
            $this->bundleCorename = str_replace(strtolower($this->bundleVendor).'_','',$this->bundleAlias);
            $this->dbDriver = $this->getDbDriver($this->bundlePath, $this->bundleAlias);
            $this->entity = $entity;
            $this->entityCC = $this->toCamelCase($entity);
            $this->entityUS = $this->toUnderscore($entity);
            $this->fields = $this->customizeFields($fields);

            $this->parameters = array(
                'entity' => $this->entity,
                'entity_cc' => $this->entityCC,
                'entity_us' => $this->entityUS,
                'fields' => $this->fields,
                'bundle_vendor' => $this->bundleVendor,
                'bundle_basename' => $this->bundleBasename,
                'bundle_name' => $this->bundleName,
                'bundle_corename' => $this->bundleCorename,
                'bundle_path' => $this->bundlePath,
                'bundle_namespace' => $this->bundleNamespace,  
                'bundle_alias' => $this->bundleAlias,          
                'db_driver' => $this->dbDriver,
                'style' => $this->style,
            );
        }    
    }

    /*
     * Renders a new file
     * 
     * @param $template The file to use as a template
     * @param $filename The location of the new file
     * @param $append Appends new code to existing file
     * 
     */
    protected function renderFile($template, $filename)
    {   
        if ($this->overwrite) {
            $newPath= $this->bundlePath;
            $filename = str_replace($this->bundlePath, $newPath, $filename);

            if (!is_dir(dirname($filename))) {
                mkdir(dirname($filename), 0777, true);
            }
        } else {
            $newPath1= $this->bundlePath.'/Temp/split/'.$this->entity;
            $filename1 = str_replace($this->bundlePath, $newPath1, $filename);

            $newPath2= $this->bundlePath.'/Temp/src';
            $filename2= str_replace($this->bundlePath, $newPath2, $filename);

            if (!is_dir(dirname($filename1))) {
                mkdir(dirname($filename1), 0777, true);
            }

            if (!is_dir(dirname($filename2))) {
                mkdir(dirname($filename2), 0777, true);
            }
        }



        $skeletonDir = __DIR__.'/../Resources/Application';

        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($skeletonDir), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ));
        $twig->addExtension(new GeneratorExtension());

        if ($this->overwrite) {
            file_put_contents($filename, $twig->render($template, $this->parameters));
        } else {
            file_put_contents($filename1, $twig->render($template, $this->parameters));
            file_put_contents($filename2, $twig->render($template, $this->parameters));
        }
    }

    /*
     * Get dbDriver from the bundle config
     * 
     * @param string $bundlePath the bundles path
     * @param string $bundleAlias the bundles alias
     * 
     * @return string the bundles db driver
     */
    protected function getDbDriver($bundlePath, $bundleAlias)
    {
        $configPath = $bundlePath.'/Resources/config/config.yml';
        $parser = new Parser();
        $config = $parser->parse(file_get_contents($configPath));
        if (is_array($config)) {
            if (array_key_exists($bundleAlias, $config)) {
                if (array_key_exists('db_driver', $config[$bundleAlias])) {
                    return $config[$bundleAlias]['db_driver'];
                }
            }
        }
        return $this->dbDriver;
    }

    /*
     * Run a console command
     *
     * @param string $command Command
     * @param array $options Command options
     */
    protected function runConsole($command, Array $options = array())
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
    function toUnderscore($str) {
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
    function toTitle($str) {
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
    function toCamelCase($str) {
        $str = lcfirst($str);
        $func = create_function('$c', 'return strtoupper($c[1]);');

        return preg_replace_callback('/_([a-z])/', $func, $str);
    }

    /*
     * Set bundles routing format
     */
    function setRoutingFormat($format) {
        $this->routingFormat = $format;
    }

    /*
     * Set update database trigger
     */
    function setUpdateDb($updateDb) {
        $this->updateDb = $updateDb;
    }

    /*
     * Set service configuration format
     */
    function setServiceConfigFormat($format) {
        $this->serviceConfigFormat = $format;
    }

    /*
     * Add custom attributes to fields
     *
     * @param array $fields
     * @return array $customizedFields 
     */
    function customizeFields($fields)
    {
        $customFields = array();
        foreach ($fields as $field) {
            switch($field['type']) {
                case 'manyToOne':
                    $targetEntity = $field['targetEntity'];
                    $arr = explode('\\', $targetEntity);
                    $field['targetVendor'] = $arr[0];
                    $field['targetBundle'] = $arr[1];
                    $field['targetBundleAlias'] = strtolower($arr[0].'_'.str_replace('Bundle', '', $arr[1]));
                    $field['targetEntityName'] = lcfirst($arr[3]);
                break;
            }

            $customFields[] = $field;
        }

        return $customFields;
    }

}
