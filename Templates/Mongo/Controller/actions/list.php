    /**
     * List {{ entityTitle }}s.
     *
     * @Route("/list/{filter}", name="{{ bundleAlias }}_{{ entityCC }}_list", defaults={"filter" = "All"})
     * @Template()
     */
    public function listAction($filter)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('{{ bundleName }}:{{ entity }}');

        ${{ entityCC }}s = $qb->getQuery()->execute();

        return array(
            '{{ entityCC }}s' => ${{ entityCC }}s,
            'filter' => $filter
        );
    }
