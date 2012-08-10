    /**
     * Show one {{ entityTitle }}.
     *
     * @Route("/show/{id}", name="{{ bundleAlias }}_{{ entityCC }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('{{ bundleName }}:{{ entity }}')
            ->find($id);

        return array(
            '{{ entityCC }}' => ${{ entityCC }},
        );
    }

