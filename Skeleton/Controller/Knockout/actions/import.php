    /**
     *  Import {{ entityTitleLC }}s via csv.
     *
     * @Route("/import", name="{{ bundleAlias }}_{{ entityCC }}_import")
     * @Template
     */
    public function importAction()
    {
        $form = $this->container->get('avro_csv.csv.form');
        $importHandler = $this->container->get('{{ bundleAlias }}.{{ entityCC }}_import.handler');

        $process = $importHandler->process();
        if ($process === true) {
            $this->container->get('session')->setFlash('success', count($importHandler->getImported()).' {{ entityTitleLC }}s imported.');

            return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'));
        } 

        return array(
            'form' => $form->createView()
        );
    }

