<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
{% include 'Mongo/Form/Type/Fields.html.twig' %}
        ;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
{% elseif field.type == 'oneToMany' or field.type == 'manyToMany' %}
//TODO
{% endif %}
{% endfor %}
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ bundleNamespace }}\Document\{{ entity }}'
        ));
    }

    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
