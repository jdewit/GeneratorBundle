<?php
namespace {{ bundle_namespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
{% if style == 'knockout' %}
use Symfony\Component\Validator\ValidatorInterface;
{% endif %}
use {{ bundle_namespace }}\Entity\{{ entity }};
use {{ bundle_namespace }}\Entity\{{ entity }}Manager;

class {{ entity }}FormHandler
{
    protected $form;
    protected $request;
{% if style == 'knockout' %}
    protected $validator;
{% endif %}
    protected ${{ entity_cc }}Manager;

    public function __construct(Form $form, Request $request,{% if style == 'knockout' %} ValidatorInterface $validator,{% endif %} {{ entity }}Manager ${{ entity_cc }}Manager)
    {
        $this->form = $form;
        $this->request = $request;  
{% if style == 'knockout' %}
        $this->validator = $validator;
{% endif %}
        $this->{{ entity_cc }}Manager = ${{ entity_cc }}Manager;
    }

    public function process({{ entity }} ${{ entity_cc }} = null)
    {
        if (null === ${{ entity_cc }}) {
            ${{ entity_cc }} = $this->{{ entity_cc }}Manager->create();
        }

        $this->form->setData(${{ entity_cc }});

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess(${{ entity_cc }});

                return true;
{% if style == 'knockout' %}
            } else { 
                $response = array();
                $errors = $this->validator->validate(${{ entity_cc }});
                foreach($errors as $error)
                {
                    $response[$error->getPropertyPath()] = $error->getMessage();
                }

                return $response;
{% endif %}
            }
        }

        return false;
    }

    protected function onSuccess({{ entity }} ${{ entity_cc }})
    {
        $this->{{ entity_cc }}Manager->update(${{ entity_cc }});
    }
}
