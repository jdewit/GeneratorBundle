    /**
     * Edit one {{ entityTitle }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit")
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('{{ bundleName }}:{{ entity }}')
            ->find($id);

        if (!${{ entityCC }}) {
            throw $this->createNotFoundException('No {{ entityCC }} found');
        }

        $form = $this->createForm(new {{ entity }}FormType(), ${{ entityCC }});

        if (true === $this->processForm($form)) {

            $this->get('session')->getFlashBag()->set('success', '{{ entity }} updated.');

            return $this->redirect($this->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'), 301);
        }

        return array(
            'form' => $form->createView(),
            '{{ entityCC }}' => ${{ entityCC }}
        );
    }

