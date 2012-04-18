    /**
     *  Get {{ entityTitleLC }} form.
     *
     * @Route("/getForm/{id}", name="{{ bundleAlias }}_{{ entityCC }}_getForm", defaults={"id"=false})
     * @Template
     */
    public function getFormAction($id)
    {
{% for field in uniqueManyToOneRelations %}
        ${{ field.targetEntityName }}s = $this->container->get('{{ field.targetBundleAlias }}.{{ field.targetEntityName | lower }}_manager')->findAllActive();
{% endfor %}
        $form = $this->container->get('form.factory')->create(new \{{ bundleNamespace }}\Form\Type\{{ entity }}FormType({% if uniqueManyToOneRelations %}array(
{% for field in uniqueManyToOneRelations %}
             '{{ field.targetEntityName }}s' => ${{ field.targetEntityName }}s,
{% endfor %}
        )
{% endif %}
        ));
        return array(
            '{{ entityCC }}Form' => $form->createView(),
{% for field in uniqueManyToOneRelations %}
            '{{ field.targetEntityName }}s' => ${{ field.targetEntityName }}s,
{% endfor %}
        );

    }

