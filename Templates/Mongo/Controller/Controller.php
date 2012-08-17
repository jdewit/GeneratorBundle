<?php

namespace {{ bundleNamespace }}\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use {{ bundleNamespace }}\Document\{{ entity }};
use {{ bundleNamespace }}\Form\Type\{{ entity }}FormType;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entityCC }}")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Controller extends Controller
{
{% for action in actions %}
    {%- include 'Mongo/Controller/actions/'~ action ~'.php' %}
{% endfor %}
}
