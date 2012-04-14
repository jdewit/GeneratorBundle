<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
{% for field in uniqueManyToOneRelations %}
use {{ field.targetVendor }}\{{ field.targetBundle }}\Entity\{{ field.targetEntityName }}Manager;
{% endfor %}


/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
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
