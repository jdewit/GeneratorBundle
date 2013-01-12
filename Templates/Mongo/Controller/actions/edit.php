    /**
     * Edit one {{ entityTitle }}, show the edit form.
     */
    public function editAction(Request $request, $id)
    {
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');
        $dispatcher = $this->container->get('event_dispatcher');

        ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

        $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.update', new {{ entity }}Event(${{ entityCC }}, $request));

        $form = $this->container->get('form.factory')->create(new {{ entity }}FormType(), ${{ entityCC }});

        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                ${{ entityCC }}Manager->update(${{ entityCC }});

                $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.updated', new {{ entity }}Event(${{ entityCC }}, $request));

                $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} updated.');

                return new RedirectResponse($this->container->get('router')->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'));
            }
        }

        return $this->container->get('templating')->renderResponse('{{ bundleName }}:{{ entity }}:edit.html.twig', array(
            'form' => $form->createView(),
            '{{ entityCC }}' => ${{ entityCC }}
        ));
    }

