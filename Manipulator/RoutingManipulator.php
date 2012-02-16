<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Manipulator;

use Symfony\Component\Yaml\Parser;

/**
 * Changes the PHP code of a YAML routing file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RoutingManipulator extends Manipulator
{
    private $filename;
    private $format;
    
    /**
     * Constructor.
     *
     * @param string $file The YAML routing file path
     */
    public function __construct($filename, $format = 'yml')
    {
        $this->filename = $filename;
        $this->format = $format;
    }

    /*
     * update the applications routing file
     * 
     * adds the bundles routing file as a resource
     *
     * @param $format The applications routing file format
     */
    public function updateAppRouting($bundleName)
    {
        switch ($this->format) {
            case 'yml':
                $parser = new Parser();
                $routingArray = $parser->parse(file_get_contents($this->filename));
                
                // only update if node does not exist
                if (in_array($bundleName, $routingArray)) {
                    return true;
                }
                        
                $current = file_get_contents($this->filename);
                $code = $bundleName.':';
                $code .= "\n";
                $code .= sprintf("    resource: \"@%s/Resources/config/routing.yml\"", $bundleName);
                $code .= "\n \n";
                $code .= $current;

                if (false === file_put_contents($this->filename, $code)) {
                    throw new \RuntimeException('Could not write to routing.yml');
                }
                
            break;
            case 'xml':
                $doc = new \DOMDocument();        
                $doc->preserveWhiteSpace = false;
                $doc->formatOutput = true;
                $doc->load($this->filename);
                //$routes = $doc->getElementById('routes')->item(0);
                //$routes = $doc->createElement('t');
                //$routes = $doc->firstChild;
                //$doc->appendChild($routes);
                $newRoute = $doc->createElement('import');
                $newRoute->setAttribute('resource', sprintf('@%s/Resources/config/routing.xml', $this->bundleName));
                $doc->documentElement->appendChild($newRoute);
               
                $xml = $doc->saveXML();
                
                //reload to format properly
                $doc->loadXML($xml);
                $doc->saveXML();
                $doc->save($this->filename);  
            break;
        }

        return true;          
        
    }

    /*
     * update a bundles routing file
     *
     * @param $bundleName
     * @param $bundleAlias
     * @param $entityUS
     * @param $entityCC
     *
     */
    public function updateBundleRouting($bundleName, $bundleAlias, $entityUS, $entity)
    {
        switch ($this->format) {
            case 'yml':
                $current = file_get_contents($this->filename);
        
                $parser = new Parser();
                $array = $parser->parse($current);
                
                if (empty($array[$bundleAlias.'_'.$entityUS])) {
                    $code = $bundleAlias.'_'.$entityUS.':';
                    $code .= "\n";
                    $code .= sprintf("    resource: \"@%s/Controller/%sController.php\"", $bundleName, $entity);
                    $code .= "\n";
                    $code .= sprintf("    type:     annotation ");
                    $code .= "\n \n";
                    $code .= $current;

                    if (false === file_put_contents($this->filename, $code)) {
                        throw new \RuntimeException('Could not write to routing.yml');
                    }
                }

            break;
        }
    }
}
