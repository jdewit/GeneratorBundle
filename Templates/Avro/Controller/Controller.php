<?php

namespace {{ bundleNamespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * {{ entity }} controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 * @Route("/{{ entityCC }}")
 */
class {{ entity }}Controller extends ContainerAware
{
{% for action in actions %}
    {%- include 'Avro/Controller/actions/'~ action ~'.html.twig' %}
{% endfor %}
}
