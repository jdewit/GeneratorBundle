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

use Symfony\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use Symfony\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Avro\GeneratorBundle\Command\Helper\DialogHelper;

abstract class GenerateAvroCommand extends DoctrineCommand
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
        $fields = array();
        $fieldMappings = $metadata->fieldMappings;
        // add field mappings
        foreach ($fieldMappings as $fieldName => $relation) {
            if ($relation['type'] == 'string') { 
                if (array_key_exists('length', $relation)) {
                    $length = $relation['length'];
                } else {
                    $length = 255;
                }
                $fields[$fieldName] =  array(
                    'fieldName' => $relation['fieldName'],
                    'type' => $relation['type'],
                    'length' => $length
                );
            } else {
                $fields[$fieldName] =  array(
                    'fieldName' => $relation['fieldName'],
                    'type' => $relation['type'],
                );
            }
        }

        // add associations
        foreach ($metadata->associationMappings as $fieldName => $relation) {
            if ($relation['type'] == ClassMetadataInfo::MANY_TO_ONE) {
                $fields[$fieldName] = array(
                    'fieldName' => $relation['fieldName'],
                    'type' => $relation['type'],
                    'mappedBy' => $relation['mappedBy']                    
                );    
            }    
            if ($relation['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $fields[$fieldName] = array(
                    'fieldName' => $relation['fieldName'],
                    'type' => $relation['type'],
                    'mappedBy' => $relation['mappedBy'],
                    'cascade' => $relation['cascade'],
                    'orphanRemoval' => $relation['orphanRemoval']                    
                );
            }
            if ($relation['type'] == ClassMetadataInfo::ONE_TO_MANY) {
                $fields[$fieldName] = array(
                    'fieldName' => $relation['fieldName'],
                    'type' => $relation['type'],
                    'mappedBy' => $relation['mappedBy'],
                    'cascade' => $relation['cascade'],
                    'orphanRemoval' => $relation['orphanRemoval']                    
                );
            }
        }

        // Remove manually managed fields
        unset($fields['id']);
        unset($fields['createdAt']);
        unset($fields['updatedAt']);

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
