
    /**
     * Delete one {{ entity }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_us }}_delete")
     * @Method("post")     
     */
    public function deleteAction($id)
    {
        ${{ entity_cc }} = $this->get('{{ bundle_alias }}.{{ entity_us }}_manager')->find{{ entity }}($id);
        $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->delete{{ entity }}(${{ entity }});
        $this->container->get('session')->setFlash('notice', '{{ entity }} deleted.');

        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_us }}_list'));     
    }
