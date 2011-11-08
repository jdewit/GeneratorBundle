<?php

namespace {{ bundle_namespace }}\Entity\Manager;

use {{ bundle_namespace }}\Entity\{{ entity }}\Manager\ManagerInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class {{ entity }}Manager implements {{ entity }}ManagerInterface
{
    protected $em;
    protected $class;
    protected $repository;

    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritDoc}
     */
    public function create{{ entity }}($name)
    {
        $class = $this->getClass();

        return new $class($name);
    }
       
    /**
     * {@inheritDoc}
     */  
    public function delete{{ entity }}({{ entity }}Interface ${{ entity_lc }})
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
    public function update{{ entity }}({{ entity }}Interface ${{ entity_lc }}, $andFlush = true)
    {
        $this->em->persist(${{ entity_lc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }
}
