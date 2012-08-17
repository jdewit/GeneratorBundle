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
            $this->get('session')->getFlashBag()->set('success', '{{ entityTitle }} created.');

            return $this->redirect($this->get('request')->headers->get('referer'), 301);
        }

        return array(
            'form' => $form->createView()
        );
    }

