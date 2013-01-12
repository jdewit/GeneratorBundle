    /**
     * List {{ entityTitle }}s.
     */
    public function listAction()
    {
        ${{ entityCC }}s = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_manager')->findAll();

        return $this->container->get('templating')->renderResponse('{{ bundleName }}:{{ entity }}:list.html.twig', array(
            '{{ entityCC }}s' => ${{ entityCC }}s
        ));
    }
