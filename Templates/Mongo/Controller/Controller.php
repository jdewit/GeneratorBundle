<?php

namespace {{ bundleNamespace }}\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerAware;

use {{ bundleNamespace }}\Document\{{ entity }};
use {{ bundleNamespace }}\Event\{{ entity }}Event;
use {{ bundleNamespace }}\Form\Type\{{ entity }}FormType;

/**
 * {{ entity }} controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Controller extends ContainerAware
{
{% for action in actions %}
    {%- include 'Mongo/Controller/actions/'~ action ~'.php' %}
{% endfor %}
}
