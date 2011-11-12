
    /**
     * Delete one {{ entity }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_delete")
     * @Method("post")     
     */
    public function deleteAction($id)
    {
        ${{ entity_lc }} = $this->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find{{ entity }}($id);
        $this->container->get('{{ bundle_alias }}.{{ entity }}_manager')->delete{{ entity }}(${{ entity }});
        $this->container->get('session')->setFlash('notice', '{{ entity }} deleted.');

        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}_list'));     
    }
