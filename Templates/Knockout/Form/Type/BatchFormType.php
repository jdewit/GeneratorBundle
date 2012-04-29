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
            ->add('selector', 'collection', array(
                'label' => 'false',
                'type' => 'checkbox',
                'attr' => array(
                    'class' => 'selector',
               )
            ))
        ;
    }
 //                    'data-bind' => "
 //                       value: id, 
 //                       checked: \$parent.checkAll(), 
 //                       attr: {'id': 'selector-' + id}
 //                   "
   
    public function getName()
    {
        return '{{ bundleAlias }}_batch';
    }
}
