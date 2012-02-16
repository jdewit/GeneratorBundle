<?php
namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

class {{ entity }}FormType extends AbstractType
{ 
    protected $owner;

    public function __construct(SecurityContextInterface $context) {
        $this->owner = $context->getToken()->getUser()->getOwner();
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% for field in fields %}
{% if field.type == 'string' %}
{% if field.fieldName == 'email' %}
            ->add('{{ field.fieldName }}', 'email', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'email',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                )
            ))          
{% elseif field.fieldName == 'country' %}
            ->add('{{ field.fieldName }}', 'country', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'required',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                )
            ))   
{% elseif field.fieldName == 'zipCode' %}
            ->add('{{ field.fieldName }}', 'text', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'required zipCode',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                )
            ))   
{% else %}
            ->add('{{ field.fieldName }}', 'text', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'required capitalize',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                )
            ))   
{% endif %}
{% elseif field.type == 'decimal' %}
            ->add('{{ field.fieldName }}', 'number', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'precision' => {{ field.precision }},
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'number required',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}

                )
            ))      
{% elseif field.type == 'text' %}
            ->add('{{ field.fieldName }}', 'textarea', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'digits required',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}

                )
            ))          
{% elseif field.type == 'datetime' %}
            ->add('{{ field.fieldName }}', 'date', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'string',
                'format' => 'dd/MM/yy', //\IntlDateFormatter::FULL
                'attr' => array(
                    'class' => 'date required',
                    'title' => 'Select a date for the {{ entity_cc }}',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                ),
            ))
{% elseif field.type == 'manyToOne' %}
            ->add('{{ field.fieldName }}', 'entity', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'class' =>'{{ field.targetEntity }}',
                'query_builder' => function ($repository) use ($owner) { return $repository->createQueryBuilder('e')->where('e.owner = ?1')->setParameter('1', $owner); },
                'attr' => array(
                    'title' => 'Choose a {{ field.fieldName }} for the {{ entity_cc }}',  
                    'class' => 'required',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: selected{{ field.fieldName|capitalize }}()
                    "
{% endif %}

                )
            ))  
{% elseif field.type == 'oneToMany' %}
            ->add('{{ field.fieldName }}', 'collection', array(
                'label' => 'false'
                'required' => false,
                'type' => new \{{ bundle_namespace }}\Form\Type\{{ field.fieldName|capitalizeFirst }}FormType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
{% elseif field.type == 'manyToMany' %}
            ->add('{{ field.fieldName }}s', 'collection', array(
                'label' => 'false'
                'required' => false,
                'type' => new \{{ bundle_namespace }}\Form\Type\{{ field.fieldName|capitalizeFirst }}FormType(),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ))
{% elseif field.type == 'boolean' %}  
            ->add('{{ field.fieldName }}', 'checkbox', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                'attr' => array(
                    'title' => '{{ field.fieldName|title }}?',  
{% if style == 'knockout' %}
                    'data-bind' => "
                        checked: {{ field.fieldName }}
                    "
{% endif %}
                )
            ))   
{% else %}
            ->add('{{ field.fieldName }}', '{{ field.type }}', array(
                'label' => '{{ field.fieldName|title }}',
                'required' => false,
                 'attr' => array(
                    'title' => '{{ field.fieldName|title }}',  
                    'class' => 'required',
{% if style == 'knockout' %}
                    'data-bind' => "
                        value: {{ field.fieldName }}
                    "
{% endif %}
                )
           ))            
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
        return '{{ bundle_alias }}_{{ entity_us }}';
    }
}
