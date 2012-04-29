    /**
     * Batch action for selected {{ entity }}s.
     *
     * @Route("/batch", name="{{ bundleAlias }}_{{ entityCC }}_batch")
     * @Method("post")     
     */
    public function batchAction()
    {
        $request = $this->container->get('request');
        switch($request->request->get('action')) {
            case 'Export':
                $writer = $this->container->get('avro_csv.writer');
                $selected = $request->request->get('selected');
                foreach ($selected as $id) {
                    ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->findAsArray($id);
                    if ($id === reset($selected)) {
                        $content = $writer->convertRow(array_keys(${{ entityCC }}));
                    }
                    if (${{ entityCC }}) {
                        $content .= $writer->convertRow(array_values(${{ entityCC }}));
                    }
                }
                $response = new Response($content);
                $response->headers->set('Content-Type', 'application/csv');
                $response->headers->set('Content-Disposition', 'attachment; filename="{{ entityCC }}s.csv"');

                return $response;
            break;
            default: 
                return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_dashboard_index'));
            break;
        }
    }

