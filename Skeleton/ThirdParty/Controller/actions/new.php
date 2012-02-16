
    /**
     * Create a new {{ entity_cc }}.
     *
     * @Route("/new", name="{{ bundle_alias }}_{{ entity_us }}_new")
     * @Template()     
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundle_alias }}.{{ entity_us }}.form');
        $formHandler = $this->container->get('{{ bundle_alias }}.{{ entity_us }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            $this->container->get('session')->setFlash('notice', '{{ entity }} created.');
            ${{ entity_cc }} = $form->getData('{{ entity_cc }}');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_us }}_edit', array('id' => ${{ entity_cc }}->getId())), 301);
        }

        return array(
            'form' => $form->createview(),
            'ajax' => $this->container->get('request')->isXmlHttpRequest(),
        );

    }
