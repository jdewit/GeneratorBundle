    
    /**
     * {{ action }} one {{ entity_cc }}.
     *
     * @Route("/{{ action }}/{id}", name="{{ bundle_alias }}_{{ entity_us }}_{{ action }}")
     * @Template()
     */
    public function {{ action }}Action($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->find{{ entity }}($id);

        return array(
            '{{ entity_cc }}' => ${{ entity_cc }},
        );
    }
