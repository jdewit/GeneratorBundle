<?php
namespace {{ bundle_namespace }}\Entity;

/*
 * @author Joris de <joris.w.dewit@gmail.com>
 */
interface {{ entity }}Interface
{
    
    function getId();
    
{% for field in fields %}
{% if field.type == "manyToOne" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    function get{{ field.fieldName|ucFirst }}();

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|ucFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }});    
{% elseif (field.type == "oneToMany" or field.type == "manyToMany") %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    function get{{ field.fieldName|ucFirst }}s();

    /**
     * Set {{ field.fieldName }}s
     *
     * @param {{ field.type }} ${{ field.fieldName }}s
     */
    function set{{ field.fieldName|ucFirst }}s(\{{ field.targetEntity }}Interface ${{ field.fieldName }}s);
    
    /**
     * Add {{ field.fieldName }}
     */
    function add{{ field.fieldName|ucFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }});i
    
    /**
     * Remove {{ field.fieldName }}
     */
    function remove{{ field.fieldName|ucFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }});
{% else %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    function get{{ field.fieldName|ucFirst }}();
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|ucFirst }}(${{ field.fieldName }});
{% endif %}       
{% endfor %}

}
