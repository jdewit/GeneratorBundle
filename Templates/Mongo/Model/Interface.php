<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ bundleNamespace }}\Model;

/**
 * {{ entity }} interface
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
interface {{ entity }}Interface
{
{% for field in fields %}
{% set adjustedFieldName = field.fieldName|slice(0, -1) %}
{% if field.type == "many" %}
    public function get{{ field.fieldName | ucFirst }}();

    public function set{{ field.fieldName | ucFirst }}(\{{ field.targetDocument }} ${{ field.fieldName }} = null);

    public function add{{ adjustedFieldName|ucFirst }}(\{{ field.targetDocument }} ${{ adjustedFieldName }});

    public function remove{{ field.adjustedFieldName | ucFirst }}(\{{ field.targetDocument }} ${{ field.fieldName }} = null);

{% elseif field.type == "one" %}
    public function get{{ field.fieldName|ucFirst }}();

    public function set{{ field.fieldName | ucFirst }}(\{{ field.targetDocument }} ${{ field.fieldName }} = null);

{% else %}
    public function get{{ field.fieldName|ucFirst }}();

    public function set{{ field.fieldName|ucFirst }}(${{ field.fieldName }});

{% endif %}
{% endfor %}
}
