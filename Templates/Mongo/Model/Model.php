<?php

/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {{ bundleNamespace }}\Model;

use JMS\SerializerBundle\Annotation\Exclude;

use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }} implements {{ entity }}Interface
{
    /**
     * @var integer
     */
    protected $id;

{% for field in fields %}
{% if field.type == "manyToOne" %}
    /**
     * @var \{{ field.targetEntity }}
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "oneToMany" %}
    /**
     * @var ArrayCollection
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "manyToMany" %}
    /**
     * @var ArrayCollection
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "string" %}
    /**
     * @var string
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "text" %}
    /**
     * @var text
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "integer" %}
    /**
     * @var integer
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "decimal" %}
    /**
     * @var decimal
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "float" %}
    /**
     * @var float
     */
    protected ${{ field.fieldName }};

{% elseif field.type == "datetime" %}
    /**
     * @var \DateTime
     */
    protected ${{ field.fieldName }};

{% else %}
    /**
     * @var {{ field.type }}
     */
    protected ${{ field.fieldName }};

{% endif %}{% endfor %}
{% if avro_generator.use_owner %}
    /**
     * @var \Avro\UserBundle\Entity\Owner
     * @exclude
     */
    protected $owner;

{% endif %}
    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var boolean
     */
    protected $isDeleted = false;

    /**
     * @var \DateTime
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

