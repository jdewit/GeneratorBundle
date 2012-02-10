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
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class Generator
{
    protected $container;
    protected $dialog;
    protected $registry;
    protected $filesystem;
    protected $output;
    protected $bundleAlias;
    protected $bundleAliasCC;
    protected $bundleCoreName;
    protected $bundleBasename;
    protected $bundleName;
    protected $bundleNamespace; 
    protected $bundlePath;
    protected $bundleVendor;
    protected $dbDriver = 'orm';
    protected $message;
    protected $thirdParty = true; 

    public function __construct($container, $dialog, OutputInterface $output, BundleInterface $bundle = null)
    {
        $this->container = $container;
        $this->dialog = $dialog;
        $this->registry = $container->get('doctrine');
        $this->filesystem = $container->get('filesystem');
        $this->output = $output;
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
        }    
// debug        
//        $output->writeln('bundlePath ='.$this->bundlePath.' bundleNamespace ='.$this->bundleNamespace.' bundleName = '.$this->bundleName.' bundleVendor ='.$this->bundleVendor.' bundleBasename ='.$this->bundleBasename.' bundleAlias = '.$this->bundleAlias.' bundleAliasCC = '.$this->bundleAliasCC.' dbDriver = '.$this->dbDriver);
//        exit;
    }
    /*
     * Renders a new file
     * 
     * @param $template The file to use as a template
     * @param $filename The location of the new file
     * @param $parameters The parameters required to generate the template
     * @param $append Appends new code to existing file
     * 
     */
    protected function renderFile($template, $filename, $parameters, $append = false)
    {   
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
        }

        $skeletonDir = __DIR__.'/../Skeleton/Application';


        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($skeletonDir), array(
            'debug'            => true,
            'cache'            => false,
            'strict_variables' => true,
            'autoescape'       => false,
        ));
        $twig->addExtension(new GeneratorExtension());

        if ($append == true) {
            file_put_contents($filename, $twig->render($template, $parameters, FILE_APPEND));
        } else {
            file_put_contents($filename, $twig->render($template, $parameters));
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

    protected function runConsole($command, Array $options = array())
    {
        $application = new Application($this->container->get('kernel'));
        $application->setAutoExit(false);        

        //$options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));

        return $application->run(new ArrayInput($options));
    }

}
