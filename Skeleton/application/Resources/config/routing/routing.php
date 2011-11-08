<?php

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;

$collection = new RouteCollection();

{% if 'list' in actions %}
$collection->add('{{ route_name_prefix }}', new Route('/', array(
    '_controller' => '{{ bundle }}:{{ entity }}:list',
)));
{% endif %}

{% if 'new' in actions %}
$collection->add('{{ route_name_prefix }}_new', new Route('/new', array(
    '_controller' => '{{ bundle }}:{{ entity }}:new',
)));
{% endif %}

{% if 'edit' in actions %}
$collection->add('{{ route_name_prefix }}_edit', new Route('/edit/{id}', array(
    '_controller' => '{{ bundle }}:{{ entity }}:edit',
)));
{% endif %}

{% if 'delete' in actions %}
$collection->add('{{ route_name_prefix }}_delete', new Route(
    '/delete/{id}',
    array('_controller' => '{{ bundle }}:{{ entity }}:delete')
));
{% endif %}

return $collection;
