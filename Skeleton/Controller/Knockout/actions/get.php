    /**
     * Get {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/get/{id}", name="{{ bundleAlias }}_{{ entityCC }}_get", defaults={"id" = false})
     * @method("post")     
     */
    public function getAction($id)
    {
        ${{ entityCC }}s = $this->container->get('avro_crm.job_manager')->findBy(array('isDeleted' => false));

        ${{ entityCC }}s = $this->container->get('serializer')->serialize(${{ entityCC }}, 'json');

        $response = new Response('{"filter": "'.$id.'", "data": '.${{ entityCC }}s.' }');
        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

