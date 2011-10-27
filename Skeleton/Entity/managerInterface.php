<?php

namespace {{ bundle_namespace }}\Entity\Manager\Interface;

interface {{ entity }}ManagerInterface
{
    /**
     * Returns an empty {{ entity_lc }} instance.
     *
     * @param string $name
     * @return {{ entity }}Interface
     */
    function create{{ entity }}($name);

    /**
     * Deletes a {{ entity_lc }}.
     *
     * @param {{ entity }}Interface ${{ entity_lc }}
     * @return void
     */
    function delete{{ entity }}({{ entity }}Interface ${{ entity_lc }});

    /**
     * Finds one {{ entity_lc }} by the given criteria.
     *
     * @param array $criteria
     * @return {{ entity }}Interface
     */
    function find{{ entity }}By(array $criteria);

    /**
     * Returns a collection with all user instances.
     *
     * @return \Traversable
     */
    function find{{ entity }}s();

    /**
     * Returns the {{ entity_lc }}'s fully qualified class name.
     *
     * @return string
     */
    function getClass();

    /**
     * Updates a {{ entity_lc }}.
     *
     * @param {{ entity }}Interface ${{ entity_lc }}
     */
    function update{{ entity }}({{ entity }}Interface ${{ entity_lc }});
}
