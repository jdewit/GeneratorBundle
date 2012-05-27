    /**
     * Restore one {{ entityTitle }}.
     *
     * @Route("/restore/{id}", name="{{ bundleAlias }}_{{ entityCC }}_restore", defaults={"id" = false})
     */
    public function restoreAction($id)
    {
        if ($id) {
            ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
            $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->restore(${{ entityCC }});
            
            $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} restored.');
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);     
    }

