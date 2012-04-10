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

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Changes the PHP code of a Kernel.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class Manipulator
{
    protected $rootDir;
    protected $bundleDir;
    protected $format;
    protected $filename;
    protected $parameters;
    protected $tokens;
    protected $line;

    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }

    public function setBundleDir($bundleDir)
    {
        $this->bundleDir = $bundleDir;
    }

    public function setFormat($format) 
    {
        $this->format = $format;
    }

    public function setFilename($filename) 
    {
        $this->filename = $filename;
    }

    public function setParameters($parameters) 
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets the code to manipulate.
     *
     * @param array   $tokens An array of PHP tokens
     * @param integer $line   The start line of the code
     */
    protected function setCode(array $tokens, $line = 0)
    {
        $this->tokens = $tokens;
        $this->line = $line;
    }

    /**
     * Gets the next token.
     *
     * @param mixed A PHP token
     */
    protected function next()
    {
        while ($token = array_shift($this->tokens)) {
            $this->line += substr_count($this->value($token), "\n");

            if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            return $token;
        }
    }

    /**
     * Gets the previous token.
     *
     * @param mixed A PHP token
     */
    protected function previous()
    {
        while ($token = array_shift($this->tokens)) {
            $this->line += substr_count($this->value($token), "\n");

            if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            return $token;
        }
    }    
    
    /**
     * Peeks the next token.
     *
     * @param mixed A PHP token
     */
    protected function peek($nb = 1)
    {
        $i = 0;
        $tokens = $this->tokens;
        while ($token = array_shift($tokens)) {
            if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
                continue;
            }

            ++$i;
            if ($i == $nb) {
                return $token;
            }
        }
    }

    /**
     * Gets the value of a token.
     *
     * @param string The token value
     */
    protected function value($token)
    {
        return is_array($token) ? $token[1] : $token;
    }
      
}
