
    /**
     * Show all {{ entity }}s.
     *
     * @Route("/list/{filter}", defaults={"filter" = "All"}, name="{{ bundle_alias }}_{{ entity_lc }}_list")
     * @Template()     
     */
    public function listAction($filter)
    {
        switch ($filter):
            case 'All':
                ${{ entity_lc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->findAll{{ entity }}s();           
            break;
            case 'Deleted':
                ${{ entity_lc }}s = $this->container->get('{{ bundle_alias }}.{{ entity_lc }}_manager')->find{{ entity }}sBy(array('isActive' => false));            
            break;            
        endswitch;      

        return array(
            '{{ entity_lc }}s' => ${{ entity_lc }}s,
            'filter' => $filter
        );
    }
