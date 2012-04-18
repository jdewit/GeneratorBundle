    /**
     * Restore one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/restore/{id}", name="{{ bundleAlias }}_{{ entityCC }}_restore", defaults={"id" = false})
     * @method("post")
     */
    public function restoreAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $process = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->restore(${{ entityCC }});
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entityTitle }} restored.",
                "data": $id
            }');
        } else {
            $response = new Response('{
                "status": "OK",
                "notice": "Unable to restore {{ entityTitle }}."
            }');
        } 
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

