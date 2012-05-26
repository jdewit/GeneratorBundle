<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
{% if style == 'knockout' %}
use Symfony\Component\Routing\RouterInterface;
{% endif %}

/*
 * Search Form for a {{ entity }}
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}SearchFormType extends AbstractType
{ 
    protected $owner;
{% if style =='knockout' %}
    protected $context;
    protected $router;
{% endif %}

    public function __construct(SecurityContextInterface $context{% if style == 'knockout' %}, RouterInterface $router{% endif %}) {
        $this->owner = $context->getToken()->getUser()->getOwner();
{% if style == 'knockout' %} 
        $this->context = $context;
        $this->router = $router;
{% endif %}

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% set searchForm = true %}
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
            ->add('orderBy', 'choice', array(
                'label' => 'Sort By',
                'choices' => array(
{% for field in fields %}{% if field.type != 'oneToMany' or field.type != 'manyToOne' %}
                    '{{ field.fieldName }}' => '{{ field.fieldName | camelCaseToTitle }}',
{% endif %}{% endfor %}
                ),
                'attr' => array(
                    'title' => 'Sort by column',
                    'chosen' => true
                )
            ))
            ->add('limit', 'choice', array(
                'label' => 'Show',
                'choices' => array(20 => '20', 50 => '50', 100 => '100'),
                'attr' => array(
                    'title' => 'Show results',
                    'data-bind' => "
                        limit: true
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
                'required' => false,
                'attr' => array(
                    'data-bind' => "
                        value: direction
                    "
                )
            ))
            ->add('offset', 'hidden', array(
                'required' => false,
                'attr' => array(
                    'data-bind' => "
                        value: offset
                    "
                )
            ))
            ->add('filter', 'hidden', array(
                'required' => false,
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
