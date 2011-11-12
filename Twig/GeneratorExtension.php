<?php
namespace Avro\GeneratorBundle\Twig;

class GeneratorExtension extends \Twig_Extension {

    public function getFilters()
    {
        return array(
            'capitalizeFirst'    => new \Twig_Filter_Function(
                '\Avro\GeneratorBundle\Twig\GeneratorExtension::capitalizeFirstFilter'
            ),
            'camelCaseToTitle'   => new \Twig_Filter_Function(
                '\Avro\GeneratorBundle\Twig\GeneratorExtension::camelCaseToTitle'
            ),
        );
    }

    public function getName()
    {
        return 'GeneratorExtension';
    }

    public static function capitalizeFirstFilter($input)
    {
        return ucfirst($input);
    }

    public static function camelCaseToTitle($input)
    {
        return trim(implode(" ", preg_split('/(?=[A-Z])/', ucfirst($input))));
    }
}
