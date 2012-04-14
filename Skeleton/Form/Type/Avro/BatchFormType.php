<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/*
 * Batch Form for a {{ entity }}
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class BatchFormType extends AbstractType
{ 
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('selected', 'collection', array(
                'type' => 'checkbox',
            ))
        ;
    }
    
    public function getName()
    {
        return '{{ bundleAlias }}_batch';
    }
}
