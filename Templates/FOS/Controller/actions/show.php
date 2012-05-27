    /**
     * Show one {{ entityTitle }}.
     *
     * @Route("/show/{id}", name="{{ bundleAlias }}_{{ entityCC }}_show")
     * @Template()
     */
    public function showAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);

        return array(
            '{{ entityCC }}' => ${{ entityCC }},
        );
    }

