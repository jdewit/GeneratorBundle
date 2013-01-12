    /**
     * Create a new {{ entityTitle }}.
     */
    public function newAction(Request $request)
    {
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');
        $dispatcher = $this->container->get('event_dispatcher');

        ${{ entityCC }} = ${{ entityCC }}Manager->create();
        $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.create', new {{ entity }}Event(${{ entityCC }}, $request));

        $form = $this->container->get('form.factory')->create(new {{ entity }}FormType(), ${{ entityCC }});

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                ${{ entityCC }}Manager->update(${{ entityCC }});

                $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.created', new {{ entity }}Event(${{ entityCC }}, $request));

                $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} created.');
            }
            return new RedirectResponse($this->container->get('router')->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'));
        }

        return $this->container->get('templating')->renderResponse('{{ bundleName }}:{{ entity }}:new.html.twig', array(
            'form' => $form->createView()
        ));
    }

