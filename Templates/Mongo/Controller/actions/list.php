    /**
     * List {{ entityTitle }}s.
     *
     * @Route("/list", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()
     */
    public function listAction()
    {
        $paginator = $this->get('application_core.paginator');
        $paginator->setClass('{{ bundleName }}:{{ entity }}');
        ${{ entityCC }}s = $paginator->getResults();


        return array(
            '{{ entityCC }}s' => ${{ entityCC }}s,
            'paginator' => $paginator
        );
    }
