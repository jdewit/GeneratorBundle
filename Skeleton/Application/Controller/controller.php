<?php

namespace {{ bundle_namespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entity_lc }}")
 */
class {{ entity }}Controller extends ContainerAware
{
    {% for action in actions %}
        {% if action == 'list' or action == 'show' or action == 'edit' or action == 'new' or action == 'delete' or action == 'batch' or action == 'getJson' %}
            {%- include 'Controller/actions/'~ action ~'.php' %}
        {% else %}
            {%- include 'Controller/actions/custom.php' %}
        {% endif %}
    {% endfor %}
       
}
