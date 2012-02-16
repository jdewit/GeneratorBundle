    
    /**
     * Show one {{ entity_cc }}.
     *
     * @Route("/show/{id}", name="{{ bundle_alias }}_{{ entity_us }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entity_cc }}' => ${{ entity_cc }},
        );
    }
