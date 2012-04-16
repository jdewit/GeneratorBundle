{% for field in fields %}
{% if field.type == 'string' %}
{% if field.fieldName == 'email' %}
            ->add('{{ field.fieldName }}', 'email', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => 'email',
                )
            ))          
{% elseif field.fieldName == 'date' %}
            ->add('{{ field.fieldName }}', 'date', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'string',
                'format' => 'yyyy-MM-dd', 
                'attr' => array(
                    'title' => 'Pick a date',
                ),
            ))
{% elseif field.fieldName == 'country' %}
            ->add('{{ field.fieldName }}', 'country', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => '',
                )
            ))   
{% elseif field.fieldName == 'zipCode' %}
            ->add('{{ field.fieldName }}', 'text', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => 'zipCode',
                )
            ))   
{% else %}
            ->add('{{ field.fieldName }}', 'text', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => 'capitalize',
                )
            ))   
{% endif %}
{% elseif field.type == 'decimal' %}
            ->add('{{ field.fieldName }}', 'number', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'precision' => {{ field.precision }},
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => 'number',

                )
            ))      
{% elseif field.type == 'text' %}
            ->add('{{ field.fieldName }}', 'textarea', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => '',
                )
            ))          
{% elseif field.type == 'datetime' %}
            ->add('{{ field.fieldName }}', 'date', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd', 
                'attr' => array(
                    'title' => 'Pick a date',
                ),
            ))
{% elseif field.type == 'manyToOne' %}
{% if field.fieldName == 'image' or field.fieldName == 'logo' %}
            ->add('image', new \Avro\AssetBundle\Form\Type\ImageFormType(), array(
                'required' => false,
                'label' => 'false'
            ))
{% else %}
            ->add('{{ field.fieldName }}', 'entity', array(
                'empty_value' => 'Select a {{ field.fieldTitle | lower }}...',
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'class' =>'{{ field.targetEntity }}',
                'query_builder' => function($er) {
                    return $er->createQueryBuilder('u');
                },
                'attr' => array(
                    'title' => 'Choose a {{ field.fieldName }}',  
                    'class' => '',
                )
            ))  
{% endif %}
{% elseif field.type == 'oneToMany' %}
{% if ignoreManyFields is not defined %}
            ->add('{{ field.fieldName }}', 'collection', array(
                'label' => 'false',
                'required' => false,
                'type' => new \{{ field.targetVendor }}\{{ field.targetBundle }}\Form\Type\{{ field.targetEntityName | ucFirst }}FormType({% if avro_generator.use_owner %}$this->router, $this->context{% endif %}),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'options' => array('data_class' => '{{ field.targetVendor }}\{{ field.targetBundle }}\Entity\{{ field.targetEntityName | ucFirst }}'), 
            ))
{% endif %}
{% elseif field.type == 'manyToMany' %}
{% if ignoreManyFields is not defined %}
            ->add('{{ field.fieldName }}', 'collection', array(
                'label' => 'false',
                'required' => false,
                'type' => new \{{ field.targetVendor }}\{{ field.targetBundle }}\Form\Type\{{ field.targetEntityName | ucFirst }}FormType({% if avro_generator.use_owner %}$this->router, $this->context{% endif %}),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'options' => array('data_class' => '{{ field.targetVendor }}\{{ field.targetBundle }}\Entity\{{ field.targetEntityName | ucFirst }}'), 
            ))
{% endif %}
{% elseif field.type == 'boolean' %}  
            ->add('{{ field.fieldName }}', 'checkbox', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                'attr' => array(
                    'title' => '{{ field.fieldTitle }}?',  
                )
            ))   
{% else %}
            ->add('{{ field.fieldName }}', '{{ field.type }}', array(
                'label' => '{{ field.fieldTitle }}',
                'required' => false,
                 'attr' => array(
                    'title' => 'Enter the {{ field.fieldTitle | lower }}',  
                    'class' => '',
                )
           ))            
{% endif %}
{% endfor %}

