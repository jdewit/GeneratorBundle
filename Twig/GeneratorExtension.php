<?php
namespace Avro\GeneratorBundle\Twig;

class GeneratorExtension extends \Twig_Extension {

    public function getFilters()
    {
        return array(
            'ucFirst'    => new \Twig_Filter_Function(
                '\Avro\GeneratorBundle\Twig\GeneratorExtension::ucFirstFilter'
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

    public static function ucFirstFilter($input)
    {
        if (is_array($input)) {
            throw new \Exception('ucFirst twig filter input must be a string');
        }
        return ucfirst($input);
    }

    public static function camelCaseToTitle($input)
    {
        if (is_array($input)) {
            throw new \Exception('ucFirst twig filter input must be a string');
        }

        return trim(implode(" ", preg_split('/(?=[A-Z])/', ucfirst($input))));
    }

    public static function camelCaseToUnderscore($str)
    {
        $str = lcfirst($str);
        $func = create_function('$c', 'return "_" . strtolower($c[1]);');

        return preg_replace_callback('/([A-Z])/', $func, $str);
    }
}
