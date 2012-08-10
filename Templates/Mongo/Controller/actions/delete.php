    /**
     * Delete one {{ entityTitle }}.
     *
     * @Route("/delete/{id}", name="{{ bundleAlias }}_{{ entityCC }}_delete", defaults={"id" = false})
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

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);
    }

