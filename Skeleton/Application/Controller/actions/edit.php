    
    /**
     * Edit one {{ entity_lc }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_edit")
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entity_lc }} = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find($id);
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form.handler');

        $process = $formHandler->process(${{ entity_lc }});
        if ($process) {
            if ($this->container->get('request')->isXmlHttpRequest()) {
                ${{ entity }}Array = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->toArray(${{ entity_lc }});
                $response = new Response(json_encode(array('message' => '{{ entity }} updated.', 'data' => ${{ entity_lc }}Array)));
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entity }} updated.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}_list'));
            }
        }

        return array(
            '{{ entity_lc}}Form' => $form->createView(),
            '{{ entity_lc }}' => ${{ entity_lc }},
        );
    }
