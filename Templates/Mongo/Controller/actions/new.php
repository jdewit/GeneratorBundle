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

        if (true === $this->processForm($form)) {
            $this->get('session')->getFlashBag()->set('success', '{{ entity }} created.');

            return $this->redirect($this->generateUrl('{{ bundleAlias }}_{{ entityCC }}_edit', array('id' => ${{ entityCC }}->getId())), 301);
        }

        return array(
            'form' => $form->createView()
        );
    }

