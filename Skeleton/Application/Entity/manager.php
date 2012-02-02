<?php
namespace {{ bundle_namespace }}\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

class {{ entity }}Manager 
{
    protected $em;
    protected $class;
    protected $repository;
    protected $owner;

    public function __construct(EntityManager $em, $class, SecurityContextInterface $context)
    {
        $this->em = $em;
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->repository = $em->getRepository($class);
        $this->owner = $context->getToken()->getUser()->getOwner();
    }

    /**
     * returns the {{ entity }}'s fully qualified class name
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Create {{ entity_lc }}
     */
    public function create()
    {
        $class = $this->getClass();
        
        ${{ entity_lc }} = new $class();
        ${{ entity_lc }}->setOwner($this->owner);

        return ${{ entity_lc }};
    }
           
    /**
     * Update {{ entity_lc }}
     */
    public function update({{ entity }} ${{ entity_lc }}, $andFlush = true)
    {
{% for field in fields %}
{% if (field.type == "oneToMany") or (field.type == "manyToMany") %}
        foreach (${{ entity_lc }}->get{{ field.fieldName }}() as ${{ field.fieldName|slice(0, -1) }}) {
            ${{ field.fieldName|slice(0, -1) }}->setOwner($this->owner);
        }
{% endif %}
{% endfor %}
        $this->em->persist(${{ entity_lc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * Soft delete one {{ entity_lc }}
     */  
    public function softDelete({{ entity }} ${{ entity_lc }})
    {
        ${{ entity_lc }}->setIsDeleted(true);
        ${{ entity_lc }}->setDeletedAt( new \Datetime('now') );
       
        $this->em->persist(${{ entity_lc }});
        $this->em->flush();
    }

    /**
     * Permanently delete one {{ entity_lc }}
     */  
    public function delete({{ entity }} ${{ entity_lc }})
    {
        $this->em->remove(${{ entity }});
        $this->em->flush();
    }

    /** 
     * Find {{ entity_lc }} as array with id as key
     */
    public function findAsKeyedArray($criteria = array()) 
    {
        ${{ entity_lc }}s = $this->findBy($criteria);

        $array = array();
        foreach( ${{ entity_lc }}s as ${{ entity_lc }} ) {
            $array[ ${{ entity_lc }}->getId() ] = $this->toArray( ${{ entity_lc }} );
        }
        
        return $array;
    }

    /**
     * Find one {{ entity_lc }} by id
     */
    public function find($id)
    {
        ${{ entity_lc }} = $this->repository->find($id);

        return ${{ entity_lc }};
    }

    /**
     * Find one {{ entity_lc }} by criteria
     *
     * @parameter $criteria
     */
    public function findOneBy($criteria = array())
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = false;
        
        return  $this->repository->findOneBy($criteria);
    }

    /**
     * Find {{ entity_lc }}s by criteria
     *
     * @parameter $criteria
     */
    public function findBy($criteria = array())
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = false;

        return $this->repository->findBy($criteria);
    }
    
    /**
     * Find all {{ entity_lc }}s 
     *
     */
    public function findAll($criteria = array())
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = false;

        return $this->repository->findBy($criteria);
    }

    /**
     * Find all deleted {{ entity }}'s
     */
    public function findAllDeleted()
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = true;

        return $this->repository->findBy($criteria);
    }

    /**
     * Convert {{ entity_lc }} entity to array
     */
    public function toArray(${{ entity_lc }})
    {
        $array = array(
{% for field in fields %}
            '{{ field.fieldName }}' => ${{ entity_lc }}->get{{ field.fieldName|capitalizeFirst }}(),
{% endfor %}
        );

        return $array;
    }

}
