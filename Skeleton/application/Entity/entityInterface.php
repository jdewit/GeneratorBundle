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
    function get{{ field.fieldName|capitalize }}();

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|capitalize }}({{ field.fieldName|capitalize }}Interface  ${{ field.fieldName }});    
{% elseif (field.type == "oneToMany" or field.type == "manyToMany") %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    function get{{ field.fieldName|capitalize }}();

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|capitalize }}({{ field.fieldName|capitalize }}Interface  ${{ field.fieldName }});
    
    /*
     * Add {{ field.fieldName }}
     */
    function add{{ field.fieldName }}({{ field.fieldName }}Interface ${{ field.fieldName }});
{% else %}
/**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    function get{{ field.fieldName|capitalize }}();
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    function set{{ field.fieldName|capitalize }}(${{ field.fieldName }});
{% endif %}       
{% endfor %}

}
