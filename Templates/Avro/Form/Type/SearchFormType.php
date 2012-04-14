<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;

/*
 * Search Form for a {{ entity }}
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}SearchFormType extends AbstractType
{ 
    protected $owner;
    protected $context;
    protected $router;

    public function __construct(SecurityContextInterface $context, RouterInterface $router) {
        $this->owner = $context->getToken()->getUser()->getOwner();
        $this->context = $context;
        $this->router = $router;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% set ignoreManyFields = true %}
{% include 'Avro/Form/Type/Fields.php' %}
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
