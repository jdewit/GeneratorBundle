    /**
     *  Get {{ entityTitleLC }} form.
     *
     * @Route("/getForm/{id}", name="{{ bundleAlias }}_{{ entityCC }}_getForm", defaults={"id"=false})
     * @Template
     */
    public function getFormAction($id)
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');

        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);

        return array(
            '{{ entityCC }}' => ${{ entityCC }},
            '{{ entityCC }}Form' => $form->createView()
        ); 
    }

