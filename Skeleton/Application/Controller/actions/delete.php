
    /**
     * Delete one {{ entity }}.
     *
     * @Route("/delete/{id}", name="{{ bundle_alias }}_{{ entity_lc }}_delete")
     */
    public function deleteAction($id)
    {
        ${{ entity_lc }} = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find($id);
        $this->container->get('{{ bundle_alias }}.{{ entity }}_manager')->softDelete(${{ entity_lc }});
        
        if ($this->container->get('request')->isXmlHttpRequest()) {
            $response = new Response(json_encode(array('message' => '{{ entity }} deleted.')));
            $response->headers->set('Content-Type', 'application/json');

            return $response; 
        } else {
            $this->container->get('session')->setFlash('success', '{{ entity }} deleted.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}_list'), 301);     
        }     
    }

