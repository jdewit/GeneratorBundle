    /**
     * Edit one {{ entityTitle }}, show the edit form.
     *
     * @Route("/edit/{id}", name="{{ bundleAlias }}_{{ entityCC }}_edit")
     * @Template()
     */
    public function editAction($id)
    {
        ${{ entityCC }} = $this->get{{ entity }}($id);

        $form = $this->createForm(new {{ entity }}FormType(), ${{ entityCC }});
        if (true === $this->processForm($form)) {
            $this->get('session')->getFlashBag()->set('success', '{{ entityTitle }} updated.');

            return $this->redirect($this->get('request')->headers->get('referer'), 301);
        }

//        $formAction = $this->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list').'?id='.$id;
//
//        parse_str(parse_url($this->get('request')->headers->get('referer'), PHP_URL_QUERY), $params);
//        foreach($params as $k => $v) {
//            if (!empty($v)) {
//                $formAction = $formAction.'&'.$k.'='.$v;
//            }
//        }

        return array(
            'form' => $form->createView(),
            '{{ entityCC }}' => ${{ entityCC }}
        );
    }

