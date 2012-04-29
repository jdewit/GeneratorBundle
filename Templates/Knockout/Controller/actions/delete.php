    /**
     * Delete one {{ entity | camelCaseToTitle | lower }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete", defaults={"id" = false})
     * @method("post")
     */
    public function deleteAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $process = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->softDelete(${{ entityCC }});

        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entityTitle }} deleted.",
                "data": "'.$id.'"
            }');
        } else {
            $response = new Response('{
                "status": "OK",
                "notice": "Unable to delete {{ entityTitle }}."
            }');
        }
        $response->headers->set('Content-Type', 'application/json');
        
        return $response; 
    }

