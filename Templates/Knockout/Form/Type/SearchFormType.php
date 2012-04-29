<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/*
 * Search Form for a {{ entity }}
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}SearchFormType extends AbstractType
{ 

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
{% set searchForm = true %}
{% include 'Knockout/Form/Type/Fields.html.twig' %}
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
            ->add('orderBy', 'choice', array(
                'label' => 'Sort By',
                'choices' => array(
{% for field in fields %}{% if field.type != 'oneToMany' or field.type != 'manyToOne' %}
                    '{{ field.fieldName }}' => '{{ field.fieldName | camelCaseToTitle }}',
{% endif %}{% endfor %}
                ),
                'attr' => array(
                    'title' => 'Sort by column',
                    'data-bind' => "
                        value: orderBy,
                        chosen: true
                    "
                )
            ))
            ->add('filter', 'choice', array(
                'label' => 'Filter',
                'choices' => array('All' => 'All', 'Deleted' => 'Deleted'),
                'attr' => array(
                    'data-bind' => "
                        value: filter,
                        chosen: true
                    "
                )
            ))
            ->add('limit', 'choice', array(
                'label' => 'Show',
                'choices' => array(15 => '15', 50 => '50', 100 => '100'),
                'attr' => array(
                    'title' => 'Show results',
                    'data-bind' => "
                        value: limit,
                        chosen: true
                    "
                )
            ))
            ->add('direction', 'hidden', array(
                'attr' => array(
                    'data-bind' => "
                        value: direction
                    "
                )
            ))
            ->add('offset', 'hidden', array(
                'attr' => array(
                    'data-bind' => "
                        value: offset
                    "
                )
            ))

        ;
    }
    
    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}_search';
    }
}
