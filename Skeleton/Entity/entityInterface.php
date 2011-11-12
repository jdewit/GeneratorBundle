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
    function get{{ field.fieldName|capitalizeFirst }}();

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|capitalizeFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }});    
{% elseif (field.type == "oneToMany" or field.type == "manyToMany") %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    function get{{ field.fieldName|capitalizeFirst }}s();

    /**
     * Set {{ field.fieldName }}s
     *
     * @param {{ field.type }} ${{ field.fieldName }}s
     */
    function set{{ field.fieldName|capitalizeFirst }}s(\{{ field.targetEntity }}Interface ${{ field.fieldName }}s);
    
    /*
     * Add {{ field.fieldName }}
     */
    function add{{ field.fieldName|capitalizeFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }});
{% else %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    function get{{ field.fieldName|capitalizeFirst }}();
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|capitalizeFirst }}(${{ field.fieldName }});
{% endif %}       
{% endfor %}

}
