     /**
     * List {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()     
     */
    public function listAction()
    {
        $searchForm = $this->container->get('{{ bundleAlias }}.{{ entityCC }}Search.form');
        $request = $this->container->get('request');
        $searchForm->bindRequest($request);

        if ('POST' == $request->getMethod()) {
            if ($searchForm->isValid()) {
                $response = new Response('{
                    "status": "OK",
                    "data": '.$this->container->get('serializer')->serialize($this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search($searchForm->getData()), 'json').'
                }');
            } else {
                $response = new Response('{
                    "status": "FAIL",
                    "notice": "Search failed. Please try again." 
                }');
            }
            $response->headers->set('Content-Type', 'application/json');

        } else {
            $response = array(
                'searchForm' => $searchForm->createView(),
                '{{ entityCC }}Form' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form')->createView(),
                '{{ entityCC }}s' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search(),
{% for field in uniqueManyToOneRelations %}
                'available{{ field.targetEntityName | ucFirst }}s' => $this->container->get('{{ field.targetBundleAlias }}.{{ field.targetEntityName }}_manager')->findAllActive(),
{% endfor %}
            );
        }

        return $response; 
    }

