    /**
     * Delete one {{ entityTitle }}.
     */
    public function deleteAction(Request $request, $id)
    {
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');
        $dispatcher = $this->container->get('event_dispatcher');

        ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

        $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.delete', new {{ entity }}Event(${{ entityCC }}, $request));

        ${{ entityCC }}->setIsDeleted(true);

        ${{ entityCC }}Manager->update(${{ entityCC }});

        $dispatcher->dispatch('{{ bundleAlias }}.{{ entity }}.deleted', new {{ entity }}Event(${{ entityCC }}, $request));

        $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} deleted.');

        return new RedirectResponse($this->container->get('router')->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'));
    }

