
    /**
     * get {{ entity_lc }} as json array.
     *
     * @Route("/getJson", name="{{ bundle_alias }}_{{ entity_lc }}_getJson")
     * @Method("post")     
     */
    public function getJsonAction()
    {
        ${{ entity_lc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->findAsKeyedArray();

        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
