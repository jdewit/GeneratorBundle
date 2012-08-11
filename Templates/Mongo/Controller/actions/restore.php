    /**
     * Restore one {{ entityTitle }}.
     *
     * @Route("/restore/{id}", name="{{ bundleAlias }}_{{ entityCC }}_restore")
     */
    public function restoreAction($id)
    {
        ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('{{ bundleName }}:{{ entity }}')
            ->find($id);

        ${{ entityCC }}->setIsDeleted(false);
        ${{ entityCC }}->setDeletedAt(null);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist(${{ entityCC }});
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', '{{ entityTitle }} restored.');

        return new RedirectResponse($this->container->get('router')->generate('{{ bundleAlias }}_{{ entityCC }}_list'), 301);
    }

