<?php
        
namespace {{ bundle_namespace }}\Entity;

use {{ bundle_namespace }}\Entity\Interface\{{ entity }}Interface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * {{ bundle_namespace }}\Entity\{{ entity }}
 * 
 * @author Joris de <joris.w.dewit@gmail.com>
 * 
 * @ORM\Entity
 */
class {{ entity }} implements {{ entity }}Interface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    protected $id;
{% for field in fields %}{% if field.fieldName != 'id' %}

    /**
{% if field.type == "manyToOne" %}
     * @ORM\ManyToOne(targetEntity="{{ field.targetEntity }}")
{% elseif field.type == "oneToMany" %}
     * @ORM\OneToMany(targetEntity="{{ field.targetEntity }}", mappedBy="{{ field.mappedBy }}", cascade={"{% for item in field.cascade %}{{ item }} {% endfor %}"}, orphanRemoval="{{ field.orphanRemoval }}"
{% elseif field.type == "manyToMany" %}  
     * @ORM\ManyToMany(targetEntity="{{ field.targetEntity }}")
     * @ORM\JoinTable(name="{{ field.joinTable }}",
     *      joinColumns={@ORM\JoinColumn(name="{{ field.mappedBy }}_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="{{ field.fieldName }}_id", referencedColumnName="id")}
     * )
{% else %}
     * @ORM\Column(name="{{ field.fieldName }}", type="{{ field.type }}", length={{ field.length }})
{% endif %}
     */    
    protected ${{ field.fieldName }}; 
{% endif %}{% endfor %}    
    
    public function __construct() 
    {
{% for field in fields %}
{% if (field.type == "oneToMany") or (field.type == "manyToMany") %} 
    $this->{{ field.fieldName }} = new ArrayCollection();
{% endif %}   
{% endfor %}
    }
    
{% for field in fields %}
{% if field.type == "manyToOne" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|capitalize }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|capitalize }}(\{{ field.targetEntity }}  ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }     
{% elseif (field.type == "oneToMany" or field.type == "manyToMany") %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|capitalize }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|capitalize }}(\{{ field.targetEntity }}  ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    } 
    
    /*
     * Add {{ field.fieldName }}
     */
    public function add{{ field.fieldName }}(\{{ field.targetEntity }} ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }}->add(${{ field.fieldName }});
        ${{ field.fieldName }}->set{{ field.mappedBy | capitalize }}($this);
    }  
{% else %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    public function get{{ field.fieldName|capitalize }}()
    {
        return $this->{{ field.fieldName }};
    }
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|capitalize }}(${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }    
{% endif %}       
{% endfor %}

}

