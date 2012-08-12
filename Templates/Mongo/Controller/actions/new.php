    /**
     * Create a new {{ entityTitle }}.
     *
     * @Route("/new", name="{{ bundleAlias }}_{{ entityCC }}_new")
     * @Template()
     */
    public function newAction()
    {
        ${{ entityCC }} = new {{ entity }}();
        $form = $this->createForm(new {{ entity }}FormType(), ${{ entityCC }});
        $form->setData(${{ entityCC }});

        if (true === $this->processForm($form)) {
            $this->get('session')->getFlashBag()->set('success', '{{ entity }} created.');

            return $this->redirect($this->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list'), 301);
        }

        return array(
            'form' => $form->createView()
        );
    }

