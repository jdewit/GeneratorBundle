    /**
     * Batch process for {{ entityTitle }}.
     *  
     * @Route("/batch", name="{{ bundleAlias }}_{{ entityCC }}_batch")
     */
    public function batchAction()
    {
        $selected = $this->container->get('request')->get('selected');

        if ($selected) {
            $action = $this->container->get('request')->get('form_action');
            
            ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

            switch ($action) {
                case 'Delete':
                    ${{ entityCC }}Manager->delete{{ entity }}s($selected);
                    $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }}s deleted.');
                break;
                case 'Restore':
                    ${{ entityCC }}Manager->restore{{ entity }}s($selected);
                    $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }}s restored.');
                break;

            }
        } else {
            $this->container->get('session')->getFlashBag()->set('notice', '0 {{ entityTitle }}s were selected.');
        }
        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}'));     
    }

