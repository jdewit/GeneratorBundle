     /**
     * List {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()     
     */
    public function listAction()
    {
        $searchForm = $this->container->get('{{ bundleAlias }}.{{ entityCC }}Search.form');
        $searchForm->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
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
            );
        }

        return $response; 
    }

