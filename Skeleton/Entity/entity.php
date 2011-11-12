<?php
namespace {{ bundle_namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use {{ bundle_namespace }}\Entity\{{ entity }}Interface;
{% for field in fields %}
{%- if (field.type == "oneToMany") or (field.type == "manyToMany") -%}
{%- if stop is not defined -%}
use Doctrine\Common\Collections\ArrayCollection;
{%- set stop = true -%}
{%- endif -%}
{%- endif -%}
{% endfor %}


/**
 * {{ bundle_namespace }}\Entity\{{ entity }}
 * 
 * @author Joris de <joris.w.dewit@gmail.com>
 * 
 * @ORM\Entity
 * @ORM\Table(name="{{ bundle_corename }}_{{ entity_lc }}s")
 * @ORM\HasLifecycleCallbacks
 */
class {{ entity }} implements {{ entity }}Interface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    protected $id;

{% for field in fields %}
{% if field.type == "manyToOne" %}
    /**
     * @var \{{ field.targetEntity }}
     *
     * @ORM\ManyToOne(targetEntity="{{ field.targetEntity }}"{% if field.cascade is not empty %}, cascade={"{% for item in field.cascade %}{% if loop.last %}{{ item }}{% else %}{{ item }} {% endif %}{% endfor %}"}{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "oneToMany" %}
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="{{ field.targetEntity }}", mappedBy="{{ field.mappedBy }}"{% if field.cascade is not empty %}, cascade={"{% for item in field.cascade %}{% if loop.last %}{{ item }}{% else %}{{ item }} {% endif %}{% endfor %}"}{% endif %}{% if field.orphanRemoval %}, orphanRemoval="true"{% endif %})
     */
    protected ${{ field.fieldName }}s;

{% elseif field.type == "manyToMany" %}
{% if field.isOwningSide %}
    /** 
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="{{ field.targetEntity }}", inversedBy="{{ field.inversedBy }}"{% if field.cascade is not empty %}, cascade={"{% for item in field.cascade %}{% if loop.last %}{{ item }}{% else %}{{ item }} {% endif %}{% endfor %}"}{% endif %})
     * @ORM\JoinTable(name="{{ bundle_corename }}_{{ entity_lc }}_{{ field.fieldName }}")
     */
{% else %}
    /** 
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="{{ field.targetEntity }}", mappedBy="{{ field.mappedBy }}"{% if field.cascade is not empty %}, cascade={"{% for item in field.cascade %}{% if loop.last %}{{ item }}{% else %}{{ item }} {% endif %}{% endfor %}"}{% endif %})
     */
{% endif %}
    protected ${{ field.fieldName }}s;

{% elseif field.type == "string" %}
    /**
     * @var string
     *
     * @ORM\Column(type="string", length={{ field.length }}{% if field.nullable %}, nullable="true"{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "datetime" %}
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected ${{ field.fieldName }};

{% else %}
    /**
     * @var {{ field.type }}
     *
     * @ORM\Column(type="{{ field.type }}"{% if field.nullable %}, nullable="true"{% endif %})
     */
    protected ${{ field.fieldName }};

{% endif %}
{% endfor %}    
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable="true")
     */
    protected $updatedAt;

    /** 
     * @ORM\PrePersist 
     */
    public function PrePersist()
    {
        $this->createdAt = new \DateTime('now');
    }

    /** 
     * @ORM\PreUpdate 
     */
    public function PreUpdate()
    {
       $this->updatedAt= new \DateTime('now');
    }

    public function __construct() 
    {
{%- for field in fields %}
    {% if (field.type == "oneToMany") or (field.type == "manyToMany") %} 
        $this->{{ field.fieldName }}s = new ArrayCollection();
    {% endif %}   
{%- endfor %}

    }

    /**
     * Get {{ entity_lc }} id
     *
     * @return integer
     */   
    public function getId()
    {
        return $this->id;
    }

{% for field in fields %}
{% if field.type == "manyToOne" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|capitalizeFirst }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|capitalizeFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }     
{% elseif (field.type == "oneToMany" or field.type == "manyToMany") %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|capitalizeFirst }}s()
    {
        return $this->{{ field.fieldName }}s;
    }

    /**
     * Set {{ field.fieldName }}s
     *
     * @param ArrayCollection ${{ field.fieldName }}s
     */
    public function set{{ field.fieldName|capitalizeFirst }}s(\{{ field.targetEntity }}Interface ${{ field.fieldName|lower }}s)
    {
        $this->{{ field.fieldName }}s = ${{ field.fieldName }}s;
    } 
    
    /**
     * Add {{ field.fieldName }} to the collection of related items
     *
     * @param \{{ field.targetEntity }} ${{ field.fieldName  }}   
     */
    public function add{{ field.fieldName|capitalizeFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }}s->add(${{ field.fieldName }});
        ${{ field.fieldName }}->set{{ field.mappedBy | capitalizeFirst }}($this);
    }  

    /**
     * Remove {{ field.fieldName }} from the collection of related items
     *
     * @param \{{ field.targetEntity }} ${{ field.fieldName  }} 
     */
    public function remove{{ field.fieldName|capitalizeFirst }}(\{{ field.targetEntity }}Interface ${{ field.fieldName }})
    {
        $this->{{ field.fieldName }}s->removeElement(${{ field.fieldName }});
    }
{% else %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    public function get{{ field.fieldName|capitalizeFirst }}()
    {
        return $this->{{ field.fieldName }};
    }
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|capitalizeFirst }}(${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }    
{% endif %}       
{% endfor %}
    
    /**
    * Set createdAt
    *
    * @param datetime $createdAt
    */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime $createdAt
     */
    public function getCreatedAt()
    {
       return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param datetime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Get updatedAt
     *
     * @return datetime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    
}

