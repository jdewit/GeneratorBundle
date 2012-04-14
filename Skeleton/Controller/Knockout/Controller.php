<?php

namespace {{ bundleNamespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entityTitle }}")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Controller extends ContainerAware
{
{% for action in actions %}
    {%- include 'Controller/Knockout/actions/'~ action ~'.html.twig' %}
{% endfor %}
}
