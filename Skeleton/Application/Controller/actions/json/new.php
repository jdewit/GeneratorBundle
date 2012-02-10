
    /**
     * Create a new {{ entity_lc }}.
     *
     * @Route("/new", name="{{ bundle_alias }}_{{ entity_lc }}_new")
     * @Template()     
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            ${{ entity_lc }} = $form->getData('{{ entity_lc }}');

            if ($this->container->get('request')->isXmlHttpRequest()) {
                $response = new Response(json_encode(array('message' => '{{ entity }} created.')));
                $response->headers->set('Content-Type', 'application/json');

                return $response; 
            } else {
                $this->container->get('session')->setFlash('success', '{{ entity }} created.');

                return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}_edit', array('id' => ${{ entity_lc }}->getId())), 301);
            }
        }

        return array(
            'form' => $form->createview(),
        );

    }