    /**
     * List {{ entityTitle }}s.
     *
     * @Route("/list/{filter}", name="{{ bundleAlias }}_{{ entityCC }}_list", defaults={"filter" = "All"})
     * @Template()     
     */
    public function listAction($filter)
    {
        $form = $this->container->get('avro_crm.clientList.form');
        $form->bindRequest($this->container->get('request'));

        if ('POST' == $this->container->get('request')->getMethod()) {
            if ($form->isValid()) {
                $action = $form['action']->getData();
                switch($action) {
                    case 'Search':
                        ${{ entityCC }}s = $this->container->get('{{ bundleAlias }}.{{ entityCC }}.manager')->search($form->getData());
                    break;
                    case 'Edit':

                    break;
                    case 'Export':

                    break;
                }
            }
        }

        return array(
            '{{ entityCC }}s' => ${{ entityCC }}s,
            'form' => $form->createView()
        );
    }   
