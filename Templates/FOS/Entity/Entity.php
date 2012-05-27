<?php
namespace {{ bundleNamespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
{% for field in fields %}
{%- if (field.type == "oneToMany") or (field.type == "manyToMany") %}
{%- if stop is not defined %}
use Doctrine\Common\Collections\ArrayCollection;
{%- set stop = true -%}
{%- endif %}
{%- endif %}
{% endfor %}
use JMS\SerializerBundle\Annotation\Exclude;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * {{ bundleNamespace }}\Entity\{{ entity }}
 * 
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 * 
 * @ORM\Entity
 * @ORM\Table(name="{{ bundleCoreName }}_{{ entityCC }}")
 * @ORM\HasLifecycleCallbacks
 */
class {{ entity }} 
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
     * @ORM\ManyToOne(targetEntity="{{ field.targetEntity }}")
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "oneToMany" %}
    /**
     * @var ArrayCollection
     * 
     * @ORM\OneToMany(targetEntity="{{ field.targetEntity }}"{% if field.mappedBy %}, mappedBy="{{ field.mappedBy }}"{% endif %}{% if field.inversedBy %}, inversedBy="{{ field.inversedBy }}"{% endif %}{% if field.cascade is not empty %}, cascade={ {% for item in field.cascade %}{% if loop.last %}"{{ item }}"{% else %}"{{ item }}",{% endif %}{% endfor %} }{% endif %}{% if field.orphanRemoval is defined %}{% if field.orphanRemoval %}, orphanRemoval=true {% endif %}{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "manyToMany" %}
    /** 
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="{{ field.targetEntity }}"{% if field.mappedBy %}, mappedBy="{{ field.mappedBy }}"{% endif %}{% if field.inversedBy %}, inversedBy="{{ field.inversedBy }}"{% endif %}{% if field.cascade is not empty %}, cascade={ {% for item in field.cascade %}{% if loop.last %}"{{ item }}"{% else %}"{{ item }}",{% endif %}{% endfor %} }{% endif %}{% if field.orphanRemoval is defined %}{% if field.orphanRemoval %}, orphanRemoval=true {% endif %}{% endif %})
     * @ORM\JoinTable(name="{{ bundleCoreName }}_{{ entityCC }}_{{ field.fieldName }}")
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "string" %}
    /**
     * @var string
     *
     * @ORM\Column(type="string"{% if field.length is defined and field.length is not empty %}, length={{ field.length }}{% endif %}{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "text" %}
    /**
     * @var text
     *
     * @ORM\Column(type="text"{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};
    
{% elseif field.type == "integer" %}
    /**
     * @var integer
     *
     * @ORM\Column(type="integer"{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "decimal" %}
    /**
     * @var decimal
     *
     * @ORM\Column(type="decimal"{% if field.precision is defined %}, precision={{ field.precision }}{% endif %}{% if field.scale is defined %}, scale={{ field.scale }}{% endif %}{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "float" %}
    /**
     * @var float
     *
     * @ORM\Column(type="float"{% if field.length is defined %}, length={{ field.length }}{% endif %}{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "datetime" %}
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime"{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% else %}
    /**
     * @var {{ field.type }}
     *
     * @ORM\Column(type="{{ field.type }}"{% if field.nullable %}, nullable=true{% endif %})
     */
    protected ${{ field.fieldName }};

{% endif %}{% endfor %}    
{% if avro_generator.use_owner %}
    /**
     * @var \Avro\UserBundle\Entity\Owner
     *
     * @ORM\ManyToOne(targetEntity="Avro\UserBundle\Entity\Owner")
     * @exclude
     */
    protected $owner;

{% endif %}
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isDeleted = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $deletedAt;

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
        $this->{{ field.fieldName }} = new ArrayCollection();
{% endif %}   
{%- endfor %}

    }

    /**
     * Get {{ entityCC }} id
     *
     * @return integer
     */   
    public function getId()
    {
        return $this->id;
    }

{% for field in fields %}
{% set adjustedFieldName = field.fieldName|slice(0, -1) %} 
{% if field.type == "manyToOne" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName | ucFirst }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName | ucFirst }}(\{{ field.targetEntity }} ${{ field.fieldName }} = null)
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }     
{% elseif field.type == "oneToMany" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|ucFirst }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param ArrayCollection ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|ucFirst }}(${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    } 

{% if false %}
    /**
     * Add {{ adjustedFieldName }} to the collection
     *
     * @param \{{ field.targetEntity }} ${{ adjustedFieldName }}   
     */
    public function add{{ adjustedFieldName|ucFirst }}(\{{ field.targetEntity }} ${{ adjustedFieldName }})
    {
        $this->{{ field.fieldName }}->add(${{ adjustedFieldName }});
{% if field.mappedBy %}
        ${{ adjustedFieldName }}->set{{ entity }}($this);
{% endif %}
    }  

    /**
     * Remove {{ field.fieldName }} from the collection of related items
     *
     * @param \{{ field.targetEntity }} ${{ field.fieldName  }} 
     */
    public function remove{{ adjustedFieldName|ucFirst }}(\{{ field.targetEntity }} ${{ adjustedFieldName }})
    {
        $this->{{ field.fieldName }}->removeElement(${{ adjustedFieldName }});
    }

{% endif %}

{% elseif field.type == "manyToMany" %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.targetEntity }} 
     */
    public function get{{ field.fieldName|ucFirst }}()
    {
        return $this->{{ field.fieldName }};
    }

    /**
     * Set {{ field.fieldName }}
     *
     * @param ArrayCollection ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|ucFirst }}(${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    } 

{% if false %}
    /**
     * Add {{ adjustedFieldName }} to the collection
     *
     * @param \{{ field.targetEntity }} ${{ adjustedFieldName }}   
     */
    public function add{{ adjustedFieldName|ucFirst }}(\{{ field.targetEntity }} ${{ adjustedFieldName }})
    {
        $this->{{ field.fieldName }}->add(${{ adjustedFieldName }});
{% if field.mappedBy %}
        ${{ adjustedFieldName }}->set{{ entity }}($this);
{% endif %}
    }  

    /**
     * Remove {{ field.fieldName }} from the collection of related items
     *
     * @param \{{ field.targetEntity }} ${{ field.fieldName  }} 
     */
    public function remove{{ adjustedFieldName|ucFirst }}(\{{ field.targetEntity }} ${{ adjustedFieldName }})
    {
        $this->{{ field.fieldName }}->removeElement(${{ adjustedFieldName }});
    }

{% endif %}
{% else %}
    /**
     * Get {{ field.fieldName }}
     * 
     * @return {{ field.type }} 
     */
    public function get{{ field.fieldName|ucFirst }}()
    {
        return $this->{{ field.fieldName }};
    }
    
    /**
     * Set {{ field.fieldName }}
     *
     * @param {{ field.type }} ${{ field.fieldName }}
     */
    public function set{{ field.fieldName|ucFirst }}(${{ field.fieldName }})
    {
        $this->{{ field.fieldName }} = ${{ field.fieldName }};
    }    

{% endif %}{% endfor %}
{% if avro_generator.use_owner %}
    /**
    * Set owner
    *
    * @param \Avro\UserBundle\Entity\Owner $owner
    */
    public function setOwner(\Avro\UserBundle\Entity\Owner $owner)
    {
        $this->owner = $owner;
    }

    /**
     * Get owner
     *
     * @return \Avro\UserBundle\Entity\Owner $owner
     */
    public function getOwner()
    {
       return $this->owner;
    }

{% endif %}
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

    /**
     * Get isDeleted
     * 
     * @return boolean 
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
    
    /**
     * Set isDeleted
     *
     * @param boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;
    }  

    /**
     * Set deletedAt
     *
     * @param datetime $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Get deletedAt
     *
     * @return datetime $deletedAt
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * String output
     */
    public function __toString()
    {
{% for field in fields %}{% if loop.first %}
        return $this->{{ field.fieldName }};
{% else %}
        //return $this->{{ field.fieldName }};
{% endif %}
{% endfor %}
    } 
}

