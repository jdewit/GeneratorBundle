<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
{% if avro_generator.use_owner %}
use Symfony\Component\Security\Core\SecurityContextInterface;
{% endif %}

/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
{ 
    protected $router;
{% if avro_generator.use_owner %}
    protected $context;
    protected $owner;
{% endif %}

    public function __construct(RouterInterface $router{% if avro_generator.use_owner %}, SecurityContextInterface $context{% endif %}) {
{% if avro_generator.use_owner %}
        $this->owner = $context->getToken()->getUser()->getOwner();
        $this->context = $context;
{% endif %}
        $this->router = $router;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% include 'Form/Type/Avro/Fields.html.twig' %}
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => '{{ bundleVendor }}\{{ bundleBaseName }}\Entity\{{ entity }}');
    }    
    
    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
