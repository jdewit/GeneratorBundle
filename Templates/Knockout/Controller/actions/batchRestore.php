    /**
     * Batch restore {{ entityTitleLC }}.
     *  
     * @Route("/batchRestore", name="{{ bundleAlias }}_{{ entityCC }}_batchRestore")
     * @method("post")
     */
    public function batchRestoreAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entityCC }}Manager->restore(${{ entityCC }}, true, true);
            } else {
                ${{ entityCC }}Manager->restore(${{ entityCC }}, false);
            }
            ++$i;
        }

        $response = new Response('{
            "status": "OK",
            "notice": "'.sprintf('%s {{ entityTitleLC }}%s restored.', $i, count($i) > 1 ? 's' : '').'"
        }');

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

