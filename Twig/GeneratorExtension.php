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
            'camelCaseToUnderscore'   => new \Twig_Filter_Function(
                '\Avro\GeneratorBundle\Twig\GeneratorExtension::camelCaseToUnderscore'
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

    public static function camelCaseToUnderscore($str)
    {
        $str = lcfirst($str);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');

        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
}
