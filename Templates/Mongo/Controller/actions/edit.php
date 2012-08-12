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

        $formAction = $this->generateUrl('{{ bundleAlias }}_{{ entityCC }}_list').'?id='.$id;

        parse_str(parse_url($this->get('request')->headers->get('referer'), PHP_URL_QUERY), $params);
        foreach($params as $k => $v) {
            if (!empty($v)) {
                $formAction = $formAction.'&'.$k.'='.$v;
            }
        }

        return array(
            'form' => $form->createView(),
            'formAction' => $formAction,
            '{{ entityCC }}' => ${{ entityCC }}
        );
    }

