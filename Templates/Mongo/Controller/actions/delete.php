    /**
     * Delete one{{ entityTitle }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete")
     */
    public function deleteAction($id)
    {
        ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('{{ bundleName }}:{{ entity }}')
            ->find($id);

        ${{ entityCC }}->setIsDeleted(true);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist(${{ entityCC }});
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} deleted.');

        $uri = $this->get('request')->headers->get('referer');

        return $this->redirect($uri);
    }

