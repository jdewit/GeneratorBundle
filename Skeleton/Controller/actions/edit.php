    /**
     * Edit one {{ entity_lc }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_edit")
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entity_lc }} = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find{{ entity }}($id);
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form.handler');

        $process = $formHandler->process(${{ entity_lc }});
        if ($process) {
            $this->container->get('session')->setFlash('notice', '{{ entity }} updated.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}_edit', array('id' => $id)));
        }

        return array(
            'form' => $form->createview(),
            '{{ entity_lc }}' => ${{ entity_lc }},
            'ajax' => $this->container->get('request')->isXmlHttpRequest(),
        );
    }
