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
     * Create {{ entity_cc }}
     */
    public function create()
    {
        $class = $this->getClass();
        
        ${{ entity_cc }} = new $class();
        ${{ entity_cc }}->setOwner($this->owner);

        return ${{ entity_cc }};
    }
           
    /**
     * Update {{ entity_cc }}
     */
    public function update({{ entity }} ${{ entity_cc }}, $andFlush = true)
    {
{% for field in fields %}
{% if (field.type == "oneToMany") or (field.type == "manyToMany") %}
        foreach (${{ entity_cc }}->get{{ field.fieldName }}() as ${{ field.fieldName|slice(0, -1) }}) {
            ${{ field.fieldName|slice(0, -1) }}->setOwner($this->owner);
        }
{% endif %}
{% endfor %}
        $this->em->persist(${{ entity_cc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * Soft delete one {{ entity_cc }}
     */  
    public function softDelete({{ entity }} ${{ entity_cc }})
    {
        ${{ entity_cc }}->setIsDeleted(true);
        ${{ entity_cc }}->setDeletedAt(new \Datetime('now'));
       
        $this->em->persist(${{ entity_cc }});
        $this->em->flush();
    }

    /**
     * Restore one {{ entity_cc }}
     */  
    public function restore({{ entity }} ${{ entity_cc }})
    {
        ${{ entity_cc }}->setIsDeleted(false);
        ${{ entity_cc }}->setDeletedAt(null);
       
        $this->em->persist(${{ entity_cc }});
        $this->em->flush();
    }

    /**
     * Permanently delete one {{ entity_cc }}
     */  
    public function delete({{ entity }} ${{ entity_cc }})
    {
        $this->em->remove(${{ entity }});
        $this->em->flush();
    }

    /**
     * Find one {{ entity_cc }} by id
     */
    public function find($id)
    {
        ${{ entity_cc }} = $this->repository->find($id);

        return ${{ entity_cc }};
    }

    /**
     * Find one {{ entity_cc }} by criteria
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
     * Find {{ entity_cc }}s by criteria
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
     * Find all {{ entity_cc }}s 
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

}
