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

use Symfony\Component\HttpKernel\Util\Filesystem;
use Symfony\Component\DependencyInjection\Container;

/**
 * Generates a bundle.
 *
 * @author Joris de Wit <joris.w.avro@gmail.com>
 */
class AvroBundleGenerator extends Generator
{     
    public function generate($vendor, $basename, $bundleNamespace, $bundleName, $dir, $dbDriver)
    {
        $filesystem = $this->container->get('filesystem');
        
        $parameters = array(
            'bundle_vendor' => $vendor,
            'bundle_namespace' => $bundleNamespace,
            'bundle_name' => $bundleName,
            'bundle_basename' => $basename,
            'bundle_alias' => strtolower($vendor.'_'.str_replace('Bundle', '', $basename)),
            'bundle_alias_cc' => $vendor.str_replace('Bundle', '', $basename),
            'db_driver' => $dbDriver,
        );
        
        //create bundle.php
        $this->renderFile('Bundle.php', $dir.'/'.$bundleName.'.php', $parameters);      
        $this->renderFile('Resources/views/layout.html.twig', $dir.'/Resources/views/layout.html.twig', $parameters);
        $this->renderFile('Resources/config/routing.yml', $dir.'/Resources/config/routing.yml', $parameters);
        $this->renderFile('Resources/config/services.yml', $dir.'/Resources/config/services.yml', $parameters);
        $this->renderFile('Resources/config/config.yml', $dir.'/Resources/config/config.yml', $parameters);
        $this->renderFile('README.md', $dir.'/README.md', $parameters);
        $this->renderFile('Resources/meta/LICENSE', $dir.'/Resources/meta/LICENSE', $parameters);
        
        //generate file structure
        $filesystem->mkdir($dir.'/Controller');
        $filesystem->mkdir($dir.'/Form');
        $filesystem->mkdir($dir.'/Form/Type');
        $filesystem->mkdir($dir.'/Form/Handler');
        
        switch ($dbDriver):
            case 'orm':
                $filesystem->mkdir($dir.'/Entity');  
            break;
            case 'mongodb':
                $filesystem->mkdir($dir.'/Document');
            break;
        endswitch;
        
        $filesystem->mkdir($dir.'/Resources/doc');
        $filesystem->mkdir($dir.'/Resources/translations');
        $filesystem->mkdir($dir.'/Resources/public/scss');
        $filesystem->mkdir($dir.'/Resources/public/images');
        $filesystem->mkdir($dir.'/Resources/public/js');

    }
}
