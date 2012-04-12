<?php
namespace {{ bundle_namespace }}\Entity;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/*
 * Managing class for {{ entity }} entity
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}Manager 
{
    protected $em;
    protected $class;
    protected $repository;
    protected $context;
    protected $owner;

    public function __construct(EntityManager $em, $class, SecurityContextInterface $context)
    {
        $this->em = $em;
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->repository = $em->getRepository($class);
        $this->context = $context;
        if ($context->getToken()) {
            if (is_object($context->getToken()->getUser())) {
                $this->owner = $context->getToken()->getUser()->getOwner();
            }
        }
    }

    /**
     * @return fully qualified class name
     */
    public function getClass()
    {
        return $this->class;
    }

    /*
     * Flush the entity manager
     *
     * @param boolean $andClear Clears instances of this class from the entity manager 
     */
    public function flush($andClear)
    {
        $this->em->flush();

        if ($andClear) {
            $this->em->clear($this->getClass());
        }
    }

    /**
     * Creates a {{ entity }}
     *
     * @return {{ entity }}
     */
    public function create()
    {
        $class = $this->getClass();
        
        ${{ entity_cc }} = new $class();
        ${{ entity_cc }}->setOwner($this->owner);

        return ${{ entity_cc }};
    }
           
    /**
     * Updates a {{ entity }}
     *
     * @param {{ entity }} ${{ entity_cc }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */
    public function update({{ entity }} ${{ entity_cc }}, $andFlush = true, $andClear = false)
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
            $this->flush($andClear);
        }
    }

    /**
     * Soft delete one {{ entity }}
     *
     * @param {{ entity }} ${{ entity_cc }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function softDelete({{ entity }} ${{ entity_cc }}, $andFlush = true, $andClear = false)
    {
        ${{ entity_cc }}->setIsDeleted(true);
        ${{ entity_cc }}->setDeletedAt(new \Datetime('now'));
       
        $this->em->persist(${{ entity_cc }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Restore one {{ entity }}
     *
     * @param {{ entity }} ${{ entity_cc }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function restore({{ entity }} ${{ entity_cc }}, $andFlush = true, $andClear = false)
    {
        ${{ entity_cc }}->setIsDeleted(false);
        ${{ entity_cc }}->setDeletedAt(null);
       
        $this->em->persist(${{ entity_cc }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Permanently delete one {{ entity }}
     *
     * @param {{ entity }} ${{ entity_cc }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function delete({{ entity }} ${{ entity_cc }}, $andFlush = true, $andClear = false)
    {
        $this->em->remove(${{ entity_cc }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Find one {{ entity_cc }} by id
     *
     * @param string $id 
     * @return {{ entity }}
     */
    public function find($id)
    {
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('Id must be specified.');
        }
        $criteria['id'] = $id;
        $criteria['owner'] = $this->owner->getId();

        ${{ entity_cc }} = $this->repository->findOneBy($criteria);

        return ${{ entity_cc }};
    }

    /*
     * Fine one {{ entity_cc }} by id as array
     *
     * @param string $id
     */
    public function findAsArray($id)
    {
        $qb = $this->em->createQueryBuilder()->select('{{ entity_cc }}')->from($this->class, '{{ entity_cc }}');
        $qb->where('{{ entity_cc }}.owner = ?1')->setParameter('1', $this->owner);
        $qb->andWhere('{{ entity_cc }}.id = ?2')->setParameter('2', $id);

        $result = $qb->getQuery()->getArrayResult();

        return current($result); 
    }

    /**
     * Find one {{ entity_cc }} by criteria
     *
     * @parameter array $criteria
     * @return {{ entity }}
     */
    public function findOneBy($criteria = array())
    {
        $criteria['owner'] = $this->owner->getId();
        
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Find {{ entity_cc }}s by criteria
     *
     * @param array $criteria
     * @param array $sortBy
     * @param string $limit
     * @return array {{ entity }}s
     */
    public function findBy(array $criteria = null, array $sortBy = null, $limit = null)
    {
        $criteria['owner'] = $this->owner->getId();

        return $this->repository->findBy($criteria, $sortBy, $limit);
    }

    /**
     * Search {{ entity_cc }}s
     * 
     * @param array $query Search criteria
     * @param string $offset 
     * @return array {{ entity }}s
     */
    public function search(array $query = array())
    {
        if (!array_key_exists('orderBy', $query)) {
            $query['orderBy'] = 'updatedAt';
        }
        if (!array_key_exists('limit', $query)) {
            $query['limit'] = '15';
        }
        if (!array_key_exists('direction', $query)) {
            $query['direction'] = 'ASC';
        }
        if (!array_key_exists('offset', $query)) {
            $query['offset'] = '0';
        }
        if (!array_key_exists('filter', $query)) {
            $query['filter'] = 'Active';
        }

        $qb = $this->em->createQueryBuilder()->select('{{ entity_cc }}')->from($this->class, '{{ entity_cc }}');
        $qb->setFirstResult($query['offset']);
        $qb->orderBy('{{ entity_cc }}.'.$query['orderBy'], $query['direction']);
        $qb->setMaxResults($query['limit']);
        $qb->where('{{ entity_cc }}.owner = ?1')->setParameter('1', $this->owner);

        switch($query['filter']) {
            case 'Active':
                $qb->andWhere('{{ entity_cc }}.isDeleted = ?2')->setParameter(2, false);
            break;
            case 'Deleted':
                $qb->andWhere('{{ entity_cc }}.isDeleted = ?2')->setParameter(2, true);
            break;
        }

        $index = 3;

        // remove non entity related fields
        unset($query['_token']);
        unset($query['offset']);
        unset($query['direction']);
        unset($query['orderBy']);
        unset($query['limit']);
        unset($query['filter']);

        foreach ($query as $key => $value) {
            if ((!empty($value)) && ($key != '_token')) {
                if (is_object($value)) { 
                    $qb->andWhere('{{ entity_cc }}.'.$key.' = ?'.$index)->setParameter($index, $value->getId());
                } elseif ($key == 'startDate') {
                    $qb->andWhere('{{ entity_cc }}.date >= ?'.$index)->setParameter($index, $value);
                } elseif ($key == 'endDate') {
                    $qb->andWhere('{{ entity_cc }}.date <= ?'.$index)->setParameter($index, $value);
                } else  {
                    $qb->andWhere('{{ entity_cc }}.'.$key.' LIKE ?'.$index)->setParameter($index, '%'.$value.'%');
                }
                $index = $index +1;
            }
        }
        $results = $qb->getQuery()->getResult();

        return $results; 
    }
}

