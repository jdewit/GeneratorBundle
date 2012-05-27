    /**
     * Create a new {{ entityTitle }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @Template()     
     */
    public function newAction()
    {
        ${{ entityCC }}Form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process();
        if ($process) {
            ${{ entityCC }} = ${{ entityCC }}Form->getData('{{ entityCC }}');
            $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} created.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_edit', array('id' => ${{ entityCC }}->getId())), 301);
        }

        return array(
            '{{ entityCC }}Form' => ${{ entityCC }}Form->createView(),
        );

    }

