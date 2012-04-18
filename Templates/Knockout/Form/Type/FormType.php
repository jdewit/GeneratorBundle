<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
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
    protected $router;
    protected $request;
{% for field in uniqueManyToOneRelations %}
    protected ${{ field.targetEntityName }}Manager;
{% endfor %}

    public function __construct(RouterInterface $router, Request $request{% for field in uniqueManyToOneRelations %}, {{ field.targetEntityName | ucFirst }}Manager ${{ field.targetEntityName }}Manager{% endfor %}) {
        $this->router = $router;
        $this->request = $request;
{% for field in uniqueManyToOneRelations %}
        $this->{{ field.targetEntityName }}Manager = ${{ field.targetEntityName }}Manager;
{% endfor %}
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
{% for field in uniqueManyToOneRelations %}
        ${{ field.targetEntityName }}s = $this->{{ field.targetEntityName }}Manager->findAllActive();
        $this->request->attributes->set('{{ field.targetEntityName }}s', ${{ field.targetEntityName }}s);
{% endfor %}

        $builder
{% include 'Knockout/Form/Type/Fields.html.twig' %}
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
