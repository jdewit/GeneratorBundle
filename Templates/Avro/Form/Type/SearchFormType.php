<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
{% if avro_generator.use_owner %}
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\SecurityContextInterface;
{% endif %}

/*
 * Search Form for a {{ entity }}
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}SearchFormType extends AbstractType
{ 
{% if avro_generator.use_owner %}
    protected $router;
    protected $owner;
    protected $context;
{% endif %}

    public function __construct({% if avro_generator.use_owner %}Router $router, SecurityContextInterface $context{% endif %}) {
{% if avro_generator.use_owner %}
        $this->router = $router;
        $this->owner = $context->getToken()->getUser()->getOwner();
        $this->context = $context;
{% endif %}
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    '{{ field.fieldName }}' => '{{ field.fieldTitle }}',
{% endif %}{% endfor %}
                ),
                'attr' => array(
                    'title' => 'Sort by column',
                    'chosen' => true
                )
            ))
            ->add('limit', 'choice', array(
                'label' => 'Show',
                'choices' => array(15 => '15', 50 => '50', 100 => '100'),
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
