<?php

namespace {{ bundle_namespace }}\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * {{ entity }} controller.
 *
 * @Route("/{{ entity_lc }}")
 */
class {{ entity }}Controller extends ContainerAware
{
    {%- if 'list' in actions %}
        {%- include 'Controller/actions/list.php' %}
    {%- endif %}
    
    {%- if 'new' in actions %}
        {%- include 'Controller/actions/new.php' %}
    {%- endif %}

    {%- if 'edit' in actions %}
        {%- include 'Controller/actions/edit.php' %}
    {%- endif %}

    {%- if 'delete' in actions %}
        {%- include 'Controller/actions/delete.php' %}
    {%- endif %}
       
}
