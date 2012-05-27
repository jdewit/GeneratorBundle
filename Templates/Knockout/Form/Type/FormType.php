<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

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
{% include 'FOS/Form/Type/Fields.html.twig' %}
        ;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
        $add{{ field.fieldName | ucFirst }} = function($form, $id) use ($builder, $router) {
            $options = array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'class' =>'{{ field.targetEntity }}',
                'choices' => array(),
                'attr' => array(
                    'title' => 'Choose a {{ field.fieldName }}',  
                    'class' => '',
                )
            );
 
            if ($id) {
                unset($options['choices']);
                $options = array_merge($options, array(
                     'query_builder' => function($repo) use ($id) {
                        $qb = $repo->createQueryBuilder('c');
                        $qb->where('c.id = ?1');
                        $qb->setParameter('1', $id);
 
                        return $qb;
                    },
                ));
            }
            $form->add($builder->getFormFactory()->createNamed('entity', '{{ field.fieldName }}', null, $options));
        };
{% elseif field.type == 'oneToMany' or field.type == 'manyToMany' %}
//TODO
{% endif %}
 
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ bundleNamespace }}\Entity\{{ entity }}'
        ));
    }

    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
