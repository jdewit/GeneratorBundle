
    /**
     * Batch process for {{ entity_lc }}.
     *  
     * @Route("/batch", name="{{ bundle_alias }}_{{ entity_lc }}_batch")
     */
    public function batchAction()
    {
        $selected = $this->container->get('request')->get('selected');

        if ($selected) {
            $action = $this->container->get('request')->get('form_action');
            
            ${{ entity_lc }}Manager = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager');

            switch ($action) {
                case 'Delete':
                    ${{ entity_lc }}Manager->delete{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} deleted.');
                break;
                case 'Restore':
                    ${{ entity_lc }}Manager->restore{{ entity }}s($selected);
                    $this->container->get('session')->setFlash('notice', '{{ entity }} restored.');
                break;

            }
        } else {
            $this->container->get('session')->setFlash('error', 'No {{ entity_lc }}s were selected.');
        }
        return new RedirectResponse($this->container->get('router')->generate('{{ bundle_alias }}_{{ entity_lc }}'));     
    }




