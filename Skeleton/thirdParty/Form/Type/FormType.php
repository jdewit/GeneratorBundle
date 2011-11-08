<?php

namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class {{ entity }}FormType extends AbstractType
{ 
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
{% for field in fields %}
{% if field.type == 'string' %}
            ->add('{{ field.fieldName }}', 'text', array(
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_lc }}'  
                )
            ))          
{% elseif field.type == 'text' %}
            ->add('{{ field.fieldName }}', 'textarea', array(
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_lc }}'  
                )
            ))          
{% elseif field.type == 'datetime' %}
            ->add('date', 'date', array(
                'attr' => array(
                    'class' => 'date'
                    'title' => 'Select a date for the {{ entitty_lc }}'
                ),
                'widget' => 'single_text',
                'input' => 'string',
                'format' => 'dd/MM/yy', //\IntlDateFormatter::FULL
            ))
{% elseif field.type == 'manyToOne' %}
            ->add('{{ field.fieldName }}', 'entity', array(
                'class' => {{ field.targetEntity }},
                'attr' => array(
                    'title' => 'Choose a {{ field.fieldName }} for the {{ entity_lc }}'  
                )
            ))  
{% elseif field.type == 'oneToMany' %}
            ->add('{{ field.fieldName }}', 'collection', array(
                'type' => new {{ field.targetEntity }}Type(),
                'allow_add' => true,
                'allow_delete' => true,
            ))
{% elseif field.type == 'ManyToMany' %}
            ->add('{{ field.fieldName }}', 'collection', array(
                'type' => new {{ field.targetEntity }}Type(),       
                'allow_add' => true,
                'allow_delete' => true,
            ))
{% else %}
            ->add('{{ field.fieldName }}')
{% endif %}
{% endfor %}

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
