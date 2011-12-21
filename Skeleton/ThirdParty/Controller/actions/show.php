    
    /**
     * Show one {{ entity_lc }}.
     *
     * @Route("/show/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entity_lc }} = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entity_lc }}' => ${{ entity_lc }},
        );
    }
