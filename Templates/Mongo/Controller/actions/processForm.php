    /**
     * Process {{ entityTitle }} form
     *
     * @param {{ entityCC }}FormType $form
     * @return boolean true is successful
     */
    public function processForm($form)
    {
        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if (true === $form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');

                ${{ entityCC }} = $form->getData();

                $dm->persist(${{ entityCC }});
                $dm->flush();

                return true;
            }
        }
    }

