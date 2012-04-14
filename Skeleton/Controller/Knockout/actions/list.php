     /**
     * List {{ entity | camelCaseToTitle | lower }}s.
     *
     * @Route("/list", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()     
     */
    public function listAction()
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}Search.form');
        $form->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
            if ($form->isValid()) {
                $response = new Response('{
                    "status" => "OK",
                    "data" => '.$this->container->get('serializer')->serialize($this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search($form->getData()), 'json').'
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
                '{{ entityCC }}s' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->search(),
                '{{ entityCC }}Form' => $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form')->createView(),
                'searchForm' => $form->createView()
            );
        }

        return $response; 
    }

