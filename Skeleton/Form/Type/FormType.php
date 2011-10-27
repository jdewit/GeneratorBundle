<?php

namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class {{ entity }}FormType extends AbstractType
{ 
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
        {%- for field in fields %}
      
            ->add('{{ field }}')
                    
        {%- endfor %}

        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => '{{ bundle_vendor }}\{{ bundle_basename }}\Entity\{{ entity }}');
    }    
    
    public function getName()
    {
        return '{{ bundle_alias }}_{{ entity_lc }}';
    }
}
