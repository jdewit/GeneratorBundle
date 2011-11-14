    
    /**
     * {{ action }} one {{ entity_lc }}.
     *
     * @Route("/{{ action }}/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_{{ action }}")
     * @Template()
     */
    public function {{ action }}Action($id)
    {
        ${{ entity_lc }} = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entity_lc }}' => ${{ entity_lc }},
        );
    }
