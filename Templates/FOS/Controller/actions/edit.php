    /**
     * Edit one {{ entityTitle }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit", defaults={"id" = false})
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        ${{ entityCC }}Form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process(${{ entityCC }});
        if ($process) {
            ${{ entityCC }} = ${{ entityCC }}Form->getData('{{ entityCC }}');
            $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} updated.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'));
        }

        return array(
            '{{ entityCC}}Form' => ${{ entityCC }}Form->createView(),
            '{{ entityCC }}' => ${{ entityCC }},
        );
    }

