    /**
     * Restore one {{ entityTitle }}.
     */
    public function restoreAction($id)
    {
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');
        $dispatcher = $this->container->get('event_dispatcher');

        ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

        $dispatcher->dispatch('{{ bundleAlias }}.{{ entityCC }}.restore', new {{ entity }}Event(${{ entityCC }}, $request));

        ${{ entityCC }}->setIsDeleted(false);
        ${{ entityCC }}->setDeletedAt(null);

        ${{ entityCC }}Manager->update(${{ entityCC }});

        $dispatcher->dispatch('{{ bundleAlias }}.{{ entityCC }}.restored', new {{ entity }}Event(${{ entityCC }}, $request));

        $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} restored.');

        return new RedirectResponse($this->container->get('router')->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'));
    }

