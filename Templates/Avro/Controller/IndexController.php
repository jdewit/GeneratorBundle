<?php

namespace {{ bundleNamespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Index controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class IndexController extends ContainerAware
{
{% for action in actions %}
    {%- include 'Avro/Controller/actions/'~ action ~'.html.twig' %}
{% endfor %}
}
