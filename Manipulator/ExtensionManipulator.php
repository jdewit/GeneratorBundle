<?php

namespace Avro\GeneratorBundle\Manipulator;

/**
 * Changes the PHP code of the bundles Extension.php file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class ExtensionManipulator extends Manipulator
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
        $this->reflected = new \ReflectionClass($parameters['bundle_namespace'].'\DependencyInjection\\'.$parameters['bundle_alias_cc'].'Extension'); 
        $this->parameters = $parameters;
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
        $method = $this->reflected->getMethod('load');
        
        $newMethod = file_get_contents($partial);
        
        $methodCall = 'if (!empty($config[\''.$this->parameters['entity_lc'].'\'])) {';
        
        $extensionCode = array_slice($src, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        // Don't add same method call twice
        if (false !== strpos(implode(' ', $extensionCode), $methodCall)) {
            return true;
        }
        
        $this->setCode(token_get_all('<?php '.implode('', $src)), $method->getEndLine());

        // merge new method
        $updatedCode = array_merge(
            array_slice($src, 0, $this->line - 1),
            array(sprintf("%s\n", $newMethod)),
            array_slice($src, $this->line - 1)
        );

        // write to file
        file_put_contents($this->filename, implode('', $updatedCode));
        return true;
    }
            
}
