    
    /**
     * Edit one {{ entity_cc }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_us }}_edit")
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entity_cc }} = $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->find{{ entity }}($id);
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_us }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_us }}.form.handler');

        $process = $formHandler->process(${{ entity_cc }});
        if ($process) {
            $this->container->get('session')->setFlash('notice', '{{ entity }} updated.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_us }}_edit', array('id' => $id)));
        }

        return array(
            'form' => $form->createview(),
            '{{ entity_cc }}' => ${{ entity_cc }},
        );
    }
