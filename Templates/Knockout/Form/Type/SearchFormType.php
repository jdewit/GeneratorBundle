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
{% for field in uniqueManyToOneRelations %}
    protected ${{ field.targetEntityName }}Manager;
{% endfor %}

    public function __construct({% for field in uniqueManyToOneRelations %}{% if not loop.first %}, {% endif %}${{ field.targetEntityName }}Manager{% endfor %}) {
{% for field in uniqueManyToOneRelations %}
        $this->{{ field.targetEntityName }}Manager = ${{ field.targetEntityName }}Manager;
{% endfor %}
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
{% for field in uniqueManyToOneRelations %}
        ${{ field.targetEntityName }}s = $this->{{ field.targetEntityName }}Manager->findAllActive();
{% endfor %}
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
                        orderBy: true,
                        chosen: true
                    "
                )
            ))
            ->add('limit', 'choice', array(
                'label' => 'Show',
                'choices' => array(20 => '20', 50 => '50', 100 => '100'),
                'attr' => array(
                    'title' => 'Show results',
                    'data-bind' => "
                        limit: true,
                        chosen: true
                    "
                )
            ))
            ->add('isDeleted', 'choice', array(
                'label' => 'Search Deleted',
                'choices' => array(0 => 'No', 1 => 'Yes'),
                'attr' => array(
                    'title' => 'Search deleted?',
                    'data-bind' => "
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
            ->add('filter', 'hidden', array(
                'attr' => array(
                    'data-bind' => "
                        value: filter
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
