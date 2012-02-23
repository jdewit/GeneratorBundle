<?php

namespace {{ bundle_namespace }}\Entity;

use {{ bundle_namespace }}\Entity\{{ entity }}Interface;

interface {{ entity }}ManagerInterface
{
    /**
     * Returns an empty {{ entity_cc }} instance.
     *
     * @param string $name
     * @return {{ entity_cc }}Interface
     */
    function create{{ entity }}($name);

    /**
     * Deletes a {{ entity_cc }}.
     *
     * @param {{ entity }}Interface ${{ entity_cc }}
     * @return void
     */
    function delete{{ entity }}({{ entity }}Interface ${{ entity_cc }});

    /**
     * Finds one {{ entity_cc }} by id.
     *
     * @param array $id
     * @return {{ entity }}Interface
     */
    function find{{ entity }}($id);

    /**
     * Finds one {{ entity_cc }} by the given criteria.
     *
     * @param array $criteria
     * @return {{ entity }}Interface
     */
    function find{{ entity }}By(array $criteria);

    /**
     * Returns a collection with all {{ entity_cc }} instances.
     *
     * @return \Traversable
     */
    function findAll{{ entity }}s();

    /**
     * Returns the {{ entity_cc }}'s fully qualified class name.
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a {{ entity_cc }}.
     *
     * @param {{ entity }}Interface ${{ entity_cc }}
     */
    function update{{ entity }}({{ entity }}Interface ${{ entity_cc }});
}
