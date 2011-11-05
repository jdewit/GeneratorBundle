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
    private $bundleName;
    
    /**
     * Constructor.
     *
     * @param string $file The YAML routing file path
     */
    public function __construct($filename, $bundleName)
    {
        $this->filename = $filename;
        $this->bundleName = $bundleName;
    }

    /**
     * Adds a routing resource at the top of the existing ones.
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If it didnt work
     */
    public function update()
    {
        switch ($this->format):
            case 'xml':
                $this->updateXml();
            break;
            case 'yml':
                $this->updateYml();
            break;
            case 'php':
                // TODO:
            break;
        endswitch;
    }
    
    protected function updateXml()
    {
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
    }
    
    /*
     * update the applications app/config/routing.yml file
     * 
     * adds the bundles routing file as a resource
     */
    public function updateAppRoutingYml()
    {
        $parser = new Parser();
        $routingArray = $parser->parse(file_get_contents($this->filename));
        
        // only update if node does not exist
        if (in_array($this->bundleName, $routingArray)) {
            throw new \RuntimeException('Bundle exists.');
        }
                
        $current = file_get_contents($this->filename);
        $code = $this->bundleName.':';
        $code .= "\n";
        $code .= sprintf("    resource: \"@%s/Resources/config/routing.yml\"", $this->bundleName);
        $code .= "\n \n";
        $code .= $current;

        if (false === file_put_contents($this->filename, $code)) {
            throw new \RuntimeException('Could not write to routing.yml');
        }

        return true;          
        
    }
}
