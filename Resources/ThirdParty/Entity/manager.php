<?php

namespace {{ bundle_namespace }}\Entity;


use {{ bundle_namespace }}\Entity\{{ entity }}Interface;
use {{ bundle_namespace }}\Entity\{{ entity }}\ManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class {{ entity }}Manager implements {{ entity }}ManagerInterface
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
        
        ${{ entity_cc }} = new $class($name);

        return ${{ entity_cc }};
    }
       
    /**
     * {@inheritDoc}
     */  
    public function delete{{ entity }}({{ entity }}Interface ${{ entity_cc }})
    {
        $this->em->remove(${{ entity }});
        $this->em->flush();
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
        ${{ entity_cc }} = $this->repository->find($id);
        if (!${{ entity_cc }}) {
            throw new NotFoundHttpException("{{ entity }} not found");
        }
        return ${{ entity_cc }};
    }

    /**
     * {@inheritDoc}
     */
    public function find{{ entity }}By(array $criteria)
    {
        ${{ entity_cc }} = $this->repository->findOneBy($criteria);
        if (!${{ entity_cc }}) {
            throw new NotFoundHttpException("{{ entity }} not found");
        }
        return ${{ entity_cc }};
    }

    /**
     * {@inheritDoc}
     */
    public function find{{ entity }}sBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
    
    /**
     * {@inheritDoc}
     */
    public function findAll{{ entity }}s()
    {
        return $this->repository->findAll();
    }
    
    /**
     * {@inheritDoc}
     */
    public function update{{ entity }}({{ entity }}Interface ${{ entity_cc }}, $andFlush = true)
    {
        $this->em->persist(${{ entity_cc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }
}
