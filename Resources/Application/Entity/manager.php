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
        foreach (${{ entity_cc }}->get{{ field.fieldName | ucFirst }}() as ${{ field.fieldName|slice(0, -1) }}) {
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
        $criteria['id'] = $id;
        $criteria['owner'] = $this->owner->getId();

        ${{ entity_cc }} = $this->repository->findOneBy($criteria);

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
        
        return  $this->repository->findOneBy($criteria);
    }

    /**
     * Find {{ entity_cc }}s by criteria
     *
     * @parameter $criteria
     */
    public function findBy(array $criteria = null, array $sortBy = null, $limit = null)
    {
        $criteria['owner'] = $this->owner->getId();

        return $this->repository->findBy($criteria, $sortBy, $limit);
    }
{% for field in fields %}
{% if field.fieldName == 'date' %}
    {% set sortColumn = 'date' %}
{% elseif field.fieldName == 'name' %}
    {% set sortColumn = 'name' %} 
{% endif %}
{% endfor %}
{% if sortColumn is not defined %}
    {% set sortColumn = 'createdAt' %} 
{% endif %}
    /**
     * Find recent {{ entity_cc }}s 
     *
     */
    public function findRecent()
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = false;

        return $this->repository->findBy($criteria, array('updatedAt' => 'DESC'), 25);
    }

    /**
     * Find all active {{ entity_cc }}s 
     *
     */
    public function findAllActive()
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = false;

        return $this->repository->findBy($criteria, array('{{ sortColumn }}' => 'DESC'));
    }

    /**
     * Find all deleted {{ entity_cc }}s 
     *
     */
    public function findAllDeleted()
    {
        $criteria['owner'] = $this->owner->getId();
        $criteria['isDeleted'] = true;

        return $this->repository->findBy($criteria, array('{{ sortColumn }}' => 'DESC'));
    }
  
    /**
     * Search {{ entity_cc }}s
     * 
     * @param array $query
     *
     * @return {{ entity_cc }}s
     */
    public function search(array $query)
    {
        $qb = $this->em->createQueryBuilder()->select('e')->from($this->class, 'e');
        $orderBy = array_pop($query);
        if ($orderBy) {
            $qb->orderBy('e.'.$orderBy, 'ASC');
        }
        $qb->where('e.owner = ?1')->setParameter('1', $this->owner);
        $index = 2;
        foreach ($query as $key => $value) {
            if ((!empty($value)) && ($key != '_token')) {
                if (is_object($value)) { 
                    $qb->andWhere('e.'.$key.' = ?'.$index)->setParameter($index, $value->getId());
                } elseif ($key == 'startDate') {
                    $qb->andWhere('e.date >= ?'.$index)->setParameter($index, $value);
                } elseif ($key == 'endDate') {
                    $qb->andWhere('e.date <= ?'.$index)->setParameter($index, $value);
                } else  {
                    $qb->andWhere('e.'.$key.' LIKE ?'.$index)->setParameter($index, '%'.$value.'%');
                }
                $index = $index +1;
            }
        }
        $results = $qb->getQuery()->getResult();

        return $results; 
    }

}
