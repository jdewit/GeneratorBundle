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
use Avro\GeneratorBundle\Yaml\Dumper;


/**
 * Changes the PHP code of the applications config file.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class ConfigManipulator extends Manipulator
{

    /**
     * Includes bundle config.yml to applications config.yml
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If it didnt work
     */
    public function update()
    {
        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);

            // Don't add same bundle twice
            if (false !== strpos($current, $this->bundleName)) {
                return true;
            }
        } else {
            throw new \RuntimeException('Could not find '. $this->file);
        }

        $this->updateConfig();
        

    }
    
    protected function updateAppConfigFile()
    {
        $parser = new Parser();
        $dumper = new Dumper();

        // get the applications config.yml and convert to php array      
        $config = $parser->parse(file_get_contents($this->file));
        
        $config['imports'][] = array('resource' => '@'.$this->bundleName.'/Resources/config/config.yml');
        
        $updatedConfig = $dumper->dump($config, 2);
        
        //file_put_contents($parameters['bundlePath'].'/Resources/config/config_temp.yml', );
        file_put_contents($this->file, $updatedConfig);
          
    }

    /**
     * Add a resource to the imports node of a yaml file
     */
    public function addResource()
    {
        $filename = $this->bundleDir.'/Resources/config/config.yml';
        $resource = '@'.$this->parameters['bundleName'].'/Resources/config/services/'.$this->parameters['entityCC'].'.yml';
        $parser = new Parser();
        $dumper = new Dumper();

        // get the applications config.yml and convert to php array      
        $config = $parser->parse(file_get_contents($filename));

        //check if resource exists
        $resourceExists = false;
        foreach($config['imports'] as $k => $v) {
            if ($v['resource'] == $resource) {
                $resourceExists = true;
            }
        }

        if ($resourceExists === false) {
            $config['imports'][] = array('resource' => $resource);
            
            $updatedConfig = $dumper->dump($config, 2);
            
            //file_put_contents($parameters['bundlePath'].'/Resources/config/config_temp.yml', );
            file_put_contents($filename, $updatedConfig);
        }
    }
   
}
