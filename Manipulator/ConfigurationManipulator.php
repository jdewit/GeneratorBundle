<?php

namespace Avro\GeneratorBundle\Manipulator;

/**
 * Changes the PHP code of the bundles Configuration.php file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class ConfigurationManipulator extends Manipulator
{
    private $filename;
    private $reflected;
    private $parameters;

    /**
     * Constructor.
     *
     * @param $filename
     * @param $bundleNamespace
     */
    public function __construct($filename, $parameters)
    {
        $this->filename = $filename;  
        $this->parameters = $parameters;
        $this->reflected = new \ReflectionClass($parameters['bundle_namespace'].'\DependencyInjection\Configuration'); 
    }

    /**
     * Adds partial to Configuration.php
     *
     * @param string $partial
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If bundle is already defined
     */
    public function addPartial($partial)
    {
        $src = file($this->filename);
        $method = $this->reflected->getMethod('getConfigTreeBuilder');
        $methods = $this->reflected->getMethods();
        $endLine = $this->reflected->getEndLine();
        $lastMethod = array_pop($methods);
        
        $newMethod = file_get_contents($partial);
        $methodCall = '$this->add'.$this->parameters['entity'].'Section($rootNode);';
        
        //add method call in getConfigTreeBuilder
        $getConfigTreeBuilder_code = array_slice($src, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        // Don't add same method call twice
        if (false !== strpos(implode('', $getConfigTreeBuilder_code), $methodCall)) {
            return true;
        }

        $this->setCode(token_get_all('<?php '.implode('', $getConfigTreeBuilder_code)), $method->getStartLine());
           
        // add the method call at the end of getConfigTreeBuilder() function
        while ($token = $this->next()) {
//            // look for return token
//            if ('return' !== $this->value($token)) {
//                continue;
//            }

            // look for $treeBuilder token
            if ('$treeBuilder' !== $this->value($token)) {
                continue;
            }

            //make sure 
            if (';' !== $this->value($this->peek())) {
                continue;
            }

            // go to next line
            $this->next();

            // merge method call
            $updatedCode = array_merge(
                array_slice($src, 0, $this->line - 1),
                array(sprintf("        %s\n\n", $methodCall)),
                array_slice($src, $this->line - 1)
            );

            // write to file
            file_put_contents($this->filename, implode('', $updatedCode));
            //return true;
        }

        // add new entitySection method

//        // Don't add same method call twice
//        if (false !== strpos(implode('', $src), $newMethod)) {
//            throw new \RuntimeException(sprintf('Method already defined in Configuration.php.'));
//        }

        $this->setCode(token_get_all('<?php '.implode('', $updatedCode)), $endLine + 1);

        // merge new method
        $updatedCodeFinal = array_merge(
            array_slice($updatedCode, 0, $this->line - 1),
            array(sprintf("%s\n", $newMethod)),
            array_slice($updatedCode, $this->line - 1)
        );

        // write to file
        file_put_contents($this->filename, implode('', $updatedCodeFinal));
        return true;
    }
            
}
