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
 */
class KernelManipulator extends Manipulator
{
    private $kernel;
    private $reflected;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->reflected = new \ReflectionObject($kernel);
    }

    /**
     * Adds a bundle at the end of the existing ones.
     *
     * @param string $bundleNamespace The bundle class name
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If bundle is already defined
     */
    public function addBundle($bundleNamespace, $bundleName)
    {
        if (!$this->reflected->getFilename()) {
            throw new \RuntimeException('Error getting the kernel');
        }

        $src = file($this->reflected->getFilename());
        $method = $this->reflected->getMethod('registerBundles');
        $lines = array_slice($src, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        // Don't add same bundle twice
        if (false !== strpos(implode('', $lines), $bundleName)) {
            return true;
        }

        $this->setCode(token_get_all('<?php '.implode('', $lines)), $method->getStartLine());
        while ($token = $this->next()) {
            // $bundles
            if (T_VARIABLE !== $token[0] || '$bundles' !== $token[1]) {
                continue;
            }

            // =
            $this->next();

            // array
            $token = $this->next();
            if (T_ARRAY !== $token[0]) {
                return false;
            }

            // add the bundle at the end of the array
            while ($token = $this->next()) {
                // look for );
                if (')' !== $this->value($token)) {
                    continue;
                }

                if (';' !== $this->value($this->peek())) {
                    continue;
                }

                // ;
                $this->next();

                $lines = array_merge(
                    array_slice($src, 0, $this->line - 1),
                    array(sprintf("            new %s(),\n", $bundleNamespace.'\\'.$bundleName)),
                    array_slice($src, $this->line - 1)
                );

                file_put_contents($this->reflected->getFilename(), implode('', $lines));

                return true;
            }
        }
    }
}
