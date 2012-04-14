    /**
     * Edit one {{ entityTitleLC }}.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit", defaults={"id" = false})
     * @method("post")
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->find($id);
        $form = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form');
        $formHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.form.handler');

        $process = $formHandler->process(${{ entityCC }});
        if ($process === true) {
            $response = new Response('{
                "status": "OK",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} updated.",
                "data": '.$this->container->get('serializer')->serialize($form->getData(), 'json').'
            }');
        } else {
            $response = new Response('{
                "status": "FAIL",
                "notice": "{{ entity | camelCaseToTitle | lower | ucFirst }} not updated.",
                "errors": '.$this->container->get('serializer')->serialize($process, 'json').'
            }');
        }

        $response->headers->set('Content-Type', 'application/json');

        return $response; 
    }

