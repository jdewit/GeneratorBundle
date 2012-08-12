    /**
     * List {{ entityTitle }}s.
     *
     * @Route("/", name="{{ bundleAlias }}_{{ entityCC }}_list")
     * @Template()
     */
    public function listAction()
    {
        $paginator = $this->get('application_core.paginator');
        $paginator->setClass('{{ bundleName }}:{{ entity }}');
        ${{ entityCC }}s = $paginator->getResults();

        $request = $this->get('request');

        $id = $request->query->get('id');
        if ($id) {
            ${{ entityCC }} = $this->get('doctrine.odm.mongodb.document_manager')
                ->getRepository('{{ bundleName }}:{{ entity }}')
                ->find($id);

            if (!${{ entityCC }}) {
                throw $this->createNotFoundException('No {{ entityCC }} found');
            }
        } else {
            ${{ entityCC }} = new {{ entity }}();
        }

        $form = $this->createForm(new {{ entity }}FormType(), ${{ entityCC }});
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if (true === $form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');

                ${{ entityCC }} = $form->getData();

                $dm->persist(${{ entityCC }});
                $dm->flush();

                if ($id) {
                    $this->get('session')->getFlashBag()->set('success', '{{ entityTitle }} updated.');
                } else {
                    $this->get('session')->getFlashBag()->set('success', '{{ entityTitle }} created.');
                }

                return $this->redirect($this->get('request')->headers->get('referer'), 301);
            }
        }

        return array(
            '{{ entityCC }}s' => ${{ entityCC }}s,
            '{{ entityCC }}' => ${{ entityCC }},
            'paginator' => $paginator,
            'form' => $form->createView()
        );
    }
