    /**
     * Batch process for {{ entityTitle }}.
     *
     * @Route("/batch", name="{{ bundleAlias }}_{{ entityCC }}_batch")
     */
    public function batchAction()
    {
        ${{ entityCC }}Ids = $this->container->get('request')->get('selected');
        $action = $this->container->get('request')->get('form_action');

        foreach(${{ entityCC }}Ids as $id) {

            ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('ApplicationCoreBundle:Country')->find($id);

            switch ($action) {
                case 'Delete':
                    ${{ entityCC }}Manager->delete{{ entity }}s($selected);
                break;
                case 'Restore':
                    ${{ entityCC }}Manager->restore{{ entity }}s($selected);
                break;
            }

            $this->container->get('session')->getFlashBag()->set('success', count(${{ entityCC }}Ids).' '.{{ entityTitle }}s.' '.$action.'d.');
        }

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}'));
    }

