<?php
namespace {{ bundleNamespace }}\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;
use Doctrine\ORM\EntityRepository;

/*
 * {{ entity }} Form Type
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormType extends AbstractType
{ 
    private $router;

    /**
     * @param string $class The {{ entity }} class name
     * @param RouterInterface $router
     * @param Request $request
     */
    public function __construct(RouterInterface $router) {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $router = $this->router;
        $builder
{% include 'Knockout/Form/Type/Fields.html.twig' %}
        ;
{% for field in fields %}
{% if field.type == 'manyToOne' %}
        $add{{ field.fieldName | ucFirst }} = function($form, $id) use ($builder, $router) {
            $options = array(
                'label' => '{{ field. fieldTitle }}',
                'required' => false,
                'class' =>'{{ field.targetEntity }}',
                'choices' => array(),
                'attr' => array(
                    'title' => 'Choose a {{ field.fieldName }}',  
                    'class' => '',
                    'data-href' => $router->generate('{{ field.targetBundleAlias }}_{{ field.targetEntityName }}_getForm'),
                    'data-bind' => "
                        value: selected{{ field.fieldName | ucFirst }},
                        options: available{{ field.targetEntityName | ucFirst }}s,
                        optionsValue: 'id',
                        optionsText: 'name',
                        optionsCaption: 'Select a {{ field.fieldTitle}}...',
                        newSelect: selected{{ field.fieldName | ucFirst }}
                    "
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
        $add{{ field.fieldName | ucFirst }} = function($form, $ids) use ($builder) {
            $options = array(
                'label' => '{{ field.fieldName | ucFirst }}',
                'required' => false,
                'multiple' => true,
                'class' =>'{{ field.targetEntity }}',
                'choices' => array(),
                'attr' => array(
                    'title' => 'Select {{ field.fieldName }}',
                    'data-bind' => "
                        optionsValue: 'id',
                        optionsText: 'name',
                        options: available{{ field.targetEntityName | ucFirst }}s,
                        selectedOptions: selected{{ field.fieldName | ucFirst }},
                        chosen: true
                    "
                )
            );
            if ($ids) {
                unset($options['choices']);
                $options = array_merge($options, array(
                     'query_builder' => function($repo) use ($ids) {
                        $qb = $repo->createQueryBuilder('c');
                        $index = 1;
                        foreach ($ids as $id) {
                            if ($index == 1) {
                                $qb->where('c.id = ?'.$index);
                            } else {
                                $qb->orWhere('c.id = ?'.$index);
                            }
                            $qb->setParameter($index, $id);
 
                            $index++;
                        }
 
                        return $qb;
                    },
                ));
            }
            $form->add($builder->getFormFactory()->createNamed('entity', '{{ field.fieldName }}', null, $options));
        };
{% endif %}
{% endfor %}
{% for field in fields %}
{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}
{% if eventsSet is not defined %}
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (DataEvent $event) use ({% for field in fields %}{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}{% if comma1Set is defined %}, {% else %}{% set comma1Set = true %}{% endif %}$add{{ field.fieldName | ucFirst }}{% endif %}{% endfor %}) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data === null) {
{% for field in fields %}
{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}
                $add{{ field.fieldName | ucFirst }}($form, null); 
{% endif %}
{% endfor %}
            } elseif (is_object($data)) {
{% for field in fields %}
{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}
                $add{{ field.fieldName | ucFirst }}($form, $data->get{{ field.fieldName | ucFirst }}()); 
{% endif %}
{% endfor %}
            }
        });
        $builder->addEventListener(FormEvents::PRE_BIND, function (DataEvent $event) use ({% for field in fields %}{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}{% if comma2Set is defined %}, {% else %}{% set comma2Set = true %}{% endif %}$add{{ field.fieldName | ucFirst }}{% endif %}{% endfor %}) {
            $form = $event->getForm();
            $data = $event->getData();

{% for field in fields %}
{% if field.type == 'manyToOne' or field.type == 'oneToMany' or field.type == 'manyToMany' %}
            if (array_key_exists('{{ field.fieldName }}', $data)) {
                $add{{ field.fieldName | ucFirst }}($form, $data['{{ field.fieldName }}']); 
            }
{% endif %}
{% endfor %}
        });
{% set eventsSet = true %}
{% endif %}
{% endif %}
{% endfor %}
 
    }

    public function getDefaultOptions()
    {
        return array(
            'data_class' => '{{ bundleNamespace }}\Entity\{{ entity }}'
        );
    }

    public function getName()
    {
        return '{{ bundleAlias }}_{{ entityCC }}';
    }
}
