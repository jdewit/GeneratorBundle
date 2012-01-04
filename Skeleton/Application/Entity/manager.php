<?php

namespace {{ bundle_namespace }}\Entity;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class {{ entity }}Manager 
{
    protected $em;
    protected $class;
    protected $repository;
    protected $context;

    public function __construct(EntityManager $em, $class, $context)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function create{{ entity }}($name)
    {
        $class = $this->getClass();
        
        ${{ entity_lc }} = new $class($name);

        return ${{ entity_lc }};
    }
       
    /**
     * {@inheritDoc}
     */  
    public function delete{{ entity }}({{ entity }} ${{ entity_lc }}, $andFlush = true)
    {

        ${{ entity_lc }}->setIsDeleted(true);
        ${{ entity_lc }}->setDeletedAt( new \Datetime('now') );
        $this->em->persist(${{ entity_lc }});
        //$this->em->remove(${{ entity }});
        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * returns the {{ entity }}'s fully qualified class name
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function find{{ entity }}($id)
    {
        ${{ entity_lc }} = $this->repository->find($id);
        if (!${{ entity_lc }}) {
            throw new NotFoundHttpException("{{ entity }} not found");
        }
        return ${{ entity_lc }};
    }

    /**
     * {@inheritDoc}
     */
    public function find{{ entity }}By(array $criteria)
    {
        $criteria['deletedAt'] = false; 
       
        ${{ entity_lc }} = $this->repository->findOneBy($criteria);
        if (!${{ entity_lc }}) {
            throw new NotFoundHttpException("{{ entity }} not found");
        }
        return ${{ entity_lc }};
    }

    /**
     * {@inheritDoc}
     */
    public function find{{ entity }}sBy(array $criteria)
    {
        $criteria['deletedAt'] = false; 
        
        return $this->repository->findBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findAll{{ entity }}s()
    {
        $criteria['deletedAt'] = false; 
        
        return $this->repository->findBy($criteria);
    }

    /**
     * Find all deleted {{ entity }}'s
     */
    public function findAllDeleted{{ entity }}s()
    {
        $criteria['deletedAt'] = true;

        return $this->repository->findBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function update{{ entity }}({{ entity }} ${{ entity_lc }}, $andFlush = true)
    {
        $this->em->persist(${{ entity_lc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }
}
