<?php
namespace {{ bundle_namespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
{% if style == 'knockout' %}
use Symfony\Component\Routing\RouterInterface;
{% endif %}
{% for field in uniqueRelations %}
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
{% if style =='knockout' %}
    protected $context;
    protected $router;
    protected $request;
{% endif %}
{% for field in uniqueRelations %}
    protected ${{ field.targetEntityName }}Manager;
{% endfor %}


    public function __construct(SecurityContextInterface $context, Request $request{% if style == 'knockout' %}, RouterInterface $router{% endif %}{% for field in uniqueRelations %}, {{ field.targetEntityName | ucFirst }}Manager ${{ field.targetEntityName }}Manager{% endfor %}) {
        $this->owner = $context->getToken()->getUser()->getOwner();
        $this->request = $request;
{% if style == 'knockout' %} 
        $this->context = $context;
        $this->router = $router;
{% endif %}
{% for field in uniqueRelations %}
        $this->{{ field.targetEntityName }}Manager = ${{ field.targetEntityName }}Manager;
{% endfor %}
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $owner = $this->owner;

{% for field in uniqueRelations %}
        ${{ field.targetEntityName }}s = $this->{{ field.targetEntityName }}Manager->findAllActive();
        $this->request->attributes->set('{{ field.targetEntityName }}s', ${{ field.targetEntityName }}s);
{% endfor %}
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
