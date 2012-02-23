<?php
namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

class {{ entity }}SearchFormType extends AbstractType
{ 
    protected $owner;

    public function __construct(SecurityContextInterface $context) {
        $this->owner = $context->getToken()->getUser()->getOwner();
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% set style = false %}
{% include 'Form/Type/Fields.html.twig' %}
{% for field in fields %}
{% if field.fieldName == 'date' %}
            ->add('startDate', 'date', array(
                'label' => 'Start Date',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'string',
                'format' => 'yyyy-MM-dd', //\IntlDateFormatter::FULL
                'attr' => array(
                    'title' => 'Pick a date',
                    'class' => 'datepicker'
                ),
            ))
            ->add('endDate', 'date', array(
                'label' => 'End Date',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'string',
                'format' => 'yyyy-MM-dd', //\IntlDateFormatter::FULL
                'attr' => array(
                    'title' => 'Pick a date',
                    'class' => 'datepicker'
                ),
            ))
{% endif %}
{% endfor %}
            ->add('isDeleted', 'checkbox', array(
                'label' => 'Search Deleted',
                'required' => false,
                'attr' => array(
                    'title' => 'Search deleted expenses?',
                )
            ))
        ;
    }
    
    public function getName()
    {
        return '{{ bundle_alias }}_{{ entity_cc }}_search';
    }
}
