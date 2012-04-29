    /**
     * Batch delete {{ entityTitleLC }}.
     *  
     * @Route("/batchDelete", name="{{ bundleAlias }}_{{ entityCC }}_batchDelete")
     * @method("post")
     */
    public function batchDeleteAction()
    {
        $selected = $this->container->get('request')->get('selected');
        ${{ entityCC }}Manager = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager');

        $i = 0;
        foreach ($selected as $id) {
            ${{ entityCC }} = ${{ entityCC }}Manager->find($id);

            if (!next($selected) || ($i % 10 == 0)) {
                ${{ entityCC }}Manager->softDelete(${{ entityCC }}, true, true);
            } else {
                ${{ entityCC }}Manager->softDelete(${{ entityCC }}, false);
            }
            ++$i;
        }

        $response = new Response('{
            "status": "OK",
            "notice": "'.sprintf('%s {{ entityTitleLC }}%s deleted.', $i, count($i) > 1 ? 's' : '').'"
        }');

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

