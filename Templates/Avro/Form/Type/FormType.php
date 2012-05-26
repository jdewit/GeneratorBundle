<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
{% if avro_generator.use_owner %}
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\SecurityContextInterface;
{% endif %}

/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
{ 
{% if avro_generator.use_owner %}
    protected $router;
    protected $context;
    protected $owner;
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
{% include 'Avro/Form/Type/Fields.php' %}
        ;
    }

    public function getDefaultOptions()
    {
        return array('data_class' => '{{ bundleVendor }}\{{ bundleBaseName }}\Entity\{{ entity }}');
    }    
    
    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
