<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Avro\GeneratorBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

abstract class GenerateAvroCommand extends ContainerAwareCommand
{
    protected function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(sprintf('The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)', $entity));
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }

    protected function getEntityMetadata($entity)
    {
        $factory = new MetadataFactory($this->getContainer()->get('doctrine'));

        return $factory->getClassMetadata($entity)->getMetadata();
    }

    protected function getFieldsFromMetadata(ClassMetadataInfo $metadata)
    {
        $fieldMappings = $metadata->fieldMappings;
        foreach ($fieldMappings as $mapping) {
            $fieldMappings[$mapping['fieldName']]['nullable'] = true;
        }
        $associationMappings = $metadata->associationMappings;
        foreach ($associationMappings as $mapping) {
            // convert association type from integer to text
            switch ($mapping['type']) {
                case "1":
                    $associationMappings[$mapping['fieldName']]['type'] = 'oneToOne';
                break;
                case "2":
                    $associationMappings[$mapping['fieldName']]['type'] = 'manyToOne'; 
                break;
                case "4":
                    $associationMappings[$mapping['fieldName']]['type'] = 'oneToMany'; 
                break;
                case "8":
                    $associationMappings[$mapping['fieldName']]['type'] = 'manyToMany';
                break;
            }
        }
        $fields = array_merge($fieldMappings, $associationMappings);
       //print_r($fields); exit;
        //Remove manually managed fields
        unset($fields['id']);
        unset($fields['owner']);
        unset($fields['createdAt']);
        unset($fields['updatedAt']);
        unset($fields['isDeleted']);
        unset($fields['deletedAt']);

        return $fields;
    }    
    
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Avro\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }    


}
