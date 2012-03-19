<?php
namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;
{% if style == 'knockout' %}
use Symfony\Component\Routing\RouterInterface;
{% endif %}

/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
{ 
    protected $owner;
{% if style =='knockout' %}
    protected $router;
{% endif %}

    public function __construct(SecurityContextInterface $context{% if style == 'knockout' %}, RouterInterface $router{% endif %}) {
        $this->owner = $context->getToken()->getUser()->getOwner();
{% if style == 'knockout' %} 
        $this->router = $router;
{% endif %}
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

        $builder
{% include 'Form/Type/Fields.html.twig' %}
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => '{{ bundle_vendor }}\{{ bundle_basename }}\Entity\{{ entity }}');
    }    
    
    public function getName()
    {
        return '{{ bundle_alias }}_{{ entity_cc }}';
    }
}
