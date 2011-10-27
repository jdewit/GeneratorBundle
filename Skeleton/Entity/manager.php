<?php

namespace {{ bundle_namespace }}\Entity\Manager;

use {{ bundle_namespace }}\Entity\Interface\{{ entity }}ManagerInterface;
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
     * Returns an empty {{ entity_lc }} instance.
     *
     * @param $name
     * @return ${{ entity }}
     */
    public function create{{ entity }}($name)
    {
        $class = $this->getClass();

        return new $class($name);
    }
       
    /**
     * deletes a {{ entity }}
     * 
     * @param ${{ entity_lc }}Interface
     * @return void
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
     * Finds one {{ entity_lc }} by the given criteria.
     *
     * @param array $criteria
     * @return {{ entity }}Interface
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
     * Finds {{ entity_lc }}s by the given criteria.
     *
     * @param array $criteria
     * @return \Traversable
    */
    public function find{{ entity }}sBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }
    
    /**
     * Finds all {{ entity_lc }}'s.
     *
     * @return \Traversable
     */
    public function findAll{{ entity }}s()
    {
        return $this->repository->findAll();
    }
    
    /**
     * Updates a {{ entity_lc }}
     *
     * @param ${{ entity_lc }}Interface
     * @param Boolean $andFlush Whether to flush the changes (default true)
     * @return void
     */
    public function update{{ entity }}({{ entity }}Interface ${{ entity_lc }}, $andFlush = true)
    {
        $this->em->persist(${{ entity_lc }});
        if ($andFlush) {
            $this->em->flush();
        }
    }
}
