    /**
     * Delete one {{ entityTitle }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete", defaults={"id" = false})
     */
    public function deleteAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->softDelete(${{ entityCC }});
            
        $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} deleted.');

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);     
    }

