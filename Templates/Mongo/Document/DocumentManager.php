<?php
namespace {{ bundleNamespace }}\Entity;

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
{% if avro_generator.use_owner %}
    protected $context;
    protected $owner;
{% endif %}

    public function __construct(EntityManager $em, $class{% if avro_generator.use_owner %}, SecurityContextInterface $context{% endif %})
    {
        $this->em = $em;
        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->repository = $em->getRepository($class);
{% if avro_generator.use_owner %}
        $this->context = $context;
        if ($context->getToken()) {
            if (is_object($context->getToken()->getUser())) {
                $this->owner = $context->getToken()->getUser()->getOwner();
            }
        }
{% endif %}
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
        
        ${{ entityCC }} = new $class();
{% if avro_generator.use_owner %}
        ${{ entityCC }}->setOwner($this->owner);
{% endif %}

        return ${{ entityCC }};
    }
           
    /**
     * Updates a {{ entity }}
     *
     * @param {{ entity }} ${{ entityCC }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */
    public function update({{ entity }} ${{ entityCC }}, $andFlush = true, $andClear = false)
    {
{% for field in fields %}
{% if (field.type == "oneToMany") or (field.type == "manyToMany") %}
        foreach (${{ entityCC }}->get{{ field.fieldName | ucFirst }}() as ${{ field.fieldName|slice(0, -1) }}) {
            // want to change this entity at all?
{% if avro_generator.use_owner %}
            ${{ field.fieldName|slice(0, -1) }}->setOwner($this->owner);
{% endif %}
        }
{% endif %}
{% endfor %}
        $this->em->persist(${{ entityCC }});

        if ($andFlush) {
            $this->flush($andClear);
        }
    }

    /**
     * Soft delete one {{ entity }}
     *
     * @param {{ entity }} ${{ entityCC }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function softDelete({{ entity }} ${{ entityCC }}, $andFlush = true, $andClear = false)
    {
        ${{ entityCC }}->setIsDeleted(true);
        ${{ entityCC }}->setDeletedAt(new \Datetime('now'));
       
        $this->em->persist(${{ entityCC }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Restore one {{ entity }}
     *
     * @param {{ entity }} ${{ entityCC }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function restore({{ entity }} ${{ entityCC }}, $andFlush = true, $andClear = false)
    {
        ${{ entityCC }}->setIsDeleted(false);
        ${{ entityCC }}->setDeletedAt(null);
       
        $this->em->persist(${{ entityCC }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Permanently delete one {{ entity }}
     *
     * @param {{ entity }} ${{ entityCC }}
     * @param boolean $andFlush Flush em if true
     * @param boolean $andClear Clear em if true
     */  
    public function delete({{ entity }} ${{ entityCC }}, $andFlush = true, $andClear = false)
    {
        $this->em->remove(${{ entityCC }});

        if ($andFlush) {
            $this->flush($andClear);
        }

        return true;
    }

    /**
     * Find one {{ entityCC }} by id
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

        ${{ entityCC }} = $this->repository->findOneBy($criteria);

        return ${{ entityCC }};
    }

    /*
     * Fine one {{ entityCC }} by id as array
     *
     * @param string $id
     */
    public function findAsArray($id)
    {
        $qb = $this->em->createQueryBuilder()->select('{{ entityCC }}')->from($this->class, '{{ entityCC }}');
        $qb->where('{{ entityCC }}.id = ?1')->setParameter('1', $id);
{% if avro_generator.use_owner %}
        $qb->andWhere('{{ entityCC }}.owner = ?2')->setParameter('2', $this->owner);
{% endif %}

        $result = $qb->getQuery()->getArrayResult();

        return current($result); 
    }

    /**
     * Find one {{ entityCC }} by criteria
     *
     * @parameter array $criteria
     * @return {{ entity }}
     */
    public function findOneBy($criteria = array())
    {
{% if avro_generator.use_owner %}
        $criteria['owner'] = $this->owner->getId();
{% endif %}
        
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Find {{ entityCC }}s by criteria
     *
     * @param array $criteria
     * @param array $sortBy
     * @param string $limit
     * @return array {{ entity }}s
     */
    public function findBy(array $criteria = null, array $sortBy = null, $limit = null)
    {
{% if avro_generator.use_owner %}
        $criteria['owner'] = $this->owner->getId();
{% endif %}
        return $this->repository->findBy($criteria, $sortBy, $limit);
    }

    /**
     * Find active {{ entityCC }}s
     *
     * @return array {{ entity }}s
     */
    public function findAllActive()
    {
        $criteria['isDeleted'] = false;
{% if avro_generator.use_owner %}
        $criteria['owner'] = $this->owner->getId();
{% endif %}

        return $this->repository->findBy($criteria);
    }

    /**
     * Search {{ entityCC }}s
     * 
     * @param array $query Search criteria
     * @return array {{ entity }}s
     */
    public function search(array $query = array(), $asArray = false)
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
            $query['filter'] = 'All';
        }

        $qb = $this->em->createQueryBuilder()->select('{{ entityCC }}')->from($this->class, '{{ entityCC }}');
        $qb->setFirstResult($query['offset']);
        $qb->orderBy('{{ entityCC }}.'.$query['orderBy'], $query['direction']);
        $qb->setMaxResults($query['limit']);
{% if avro_generator.use_owner %}
        $qb->where('{{ entityCC }}.owner = ?1')->setParameter('1', $this->owner);
{% endif %}

        $filter = $query['filter'];
        if (is_numeric($filter)) {
            $qb->andWhere('{{ entityCC }}.id = ?2')->setParameter(2, $filter);
        } else {
            switch($filter) {
                case 'All':
                    $qb->andWhere('{{ entityCC }}.isDeleted = ?2')->setParameter(2, false);
                break;
                case 'Deleted':
                    $qb->andWhere('{{ entityCC }}.isDeleted = ?2')->setParameter(2, true);
                break;
                default:
                    $qb->andWhere('{{ entityCC }}.isDeleted = ?2')->setParameter(2, false);
                break;
            }
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
                    $qb->andWhere('{{ entityCC }}.'.$key.' = ?'.$index)->setParameter($index, $value->getId());
                } elseif ($key == 'startDate') {
                    $qb->andWhere('{{ entityCC }}.date >= ?'.$index)->setParameter($index, $value);
                } elseif ($key == 'endDate') {
                    $qb->andWhere('{{ entityCC }}.date <= ?'.$index)->setParameter($index, $value);
                } else  {
                    $qb->andWhere('{{ entityCC }}.'.$key.' LIKE ?'.$index)->setParameter($index, '%'.$value.'%');
                }
                $index = $index +1;
            }
        }

        if (true === $asArray) {
            $results = $qb->getQuery()->getArrayResult();
        } else {
            $results = $qb->getQuery()->getResult();
        }

        return $results; 
    }
}

