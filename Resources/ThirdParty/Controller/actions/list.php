
    /**
     * Show all {{ entity }}s.
     *
     * @Route("/list/{filter}", defaults={"filter" = "All"}, name="{{ bundle_alias }}_{{ entity_us }}_list")
     * @Template()     
     */
    public function listAction($filter)
    {
        switch ($filter):
            case 'All':
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->findAll{{ entity }}s();           
            break;
            case 'Deleted':
                ${{ entity_cc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_us }}_manager')->find{{ entity }}sBy(array('isActive' => false));            
            break;            
        endswitch;      

        return array(
            '{{ entity_cc }}s' => ${{ entity_cc }}s,
            'filter' => $filter
        );
    }
