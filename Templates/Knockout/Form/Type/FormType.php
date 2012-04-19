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
    private $class;
    private $router;
    private $request;
{% for field in uniqueManyToOneRelations %}
    private ${{ field.targetEntityName }}Manager;
{% endfor %}

    /**
     * @param string $class The {{ entity }} class name
     * @param RouterInterface $router
     * @param Request $request
     */
    public function __construct($class, RouterInterface $router, Request $request{% for field in uniqueManyToOneRelations %}, {{ field.targetEntityName | ucFirst }}Manager ${{ field.targetEntityName }}Manager{% endfor %}) {
        $this->class = $class;
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

    public function getDefaultOptions()
    {
        return array(
            'data_class' => $this->class
        );
    }

    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
