<?php
namespace {{ bundleNamespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use {{ bundleNamespace }}\Entity\{{ entity }};
use {{ bundleNamespace }}\Entity\{{ entity }}Manager;

/*
 * {{ entity | camelCaseToTitle }} Form Handler
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormHandler
{
    protected $form;
    protected $request;
    protected ${{ entityCC }}Manager;

    public function __construct(Form $form, Request $request, {{ entity }}Manager ${{ entityCC }}Manager)
    {
        $this->form = $form;
        $this->request = $request;  
        $this->{{ entityCC }}Manager = ${{ entityCC }}Manager;
    }

    /*
     * Process the form
     *
     * @param {{ entity }} 
     *
     * @return boolean true if successful
     * @return array $errors if unsuccessful
     */
    public function process({{ entity }} ${{ entityCC }} = null)
    {
        if (null === ${{ entityCC }}) {
            ${{ entityCC }} = $this->{{ entityCC }}Manager->create();
        }

        $this->form->setData(${{ entityCC }});

        if ('POST' == $this->request->getMethod()) {
            $this->form->bindRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess(${{ entityCC }});

                return true;
{% if style == 'knockout' %}
            } else { 
                $response = array();
                foreach ($this->form->getChildren() as $field) {
                    $errors = $field->getErrors();
                    if ($errors) {
                        $response[$field->getName()] = strtr($errors[0]->getMessageTemplate(), $errors[0]->getMessageParameters());
                    }
                }

                return $response;
            }
{% endif %}
        }

        return false;
    }

    /*
     * Update {{ entity }} if valid
     *
     * @param {{ entity }}
     */
    protected function onSuccess({{ entity }} ${{ entityCC }})
    {
        $this->{{ entityCC }}Manager->update(${{ entityCC }});
    }
}
