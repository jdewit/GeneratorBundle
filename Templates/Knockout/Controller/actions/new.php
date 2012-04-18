    /**
     * Create a new {{ entityTitleLC }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @method("post")
     */
    public function newAction()
    {
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process();
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "action": "new",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} created.",
                "data": '.$this->container->get('serializer')->serialize($form->getData(), 'json').'
            }');
        } else {
            $response = new Response('{
                "status": "FAIL",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not created.",
                "errors": '.$this->container->get('serializer')->serialize($process, 'json').'
            }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

