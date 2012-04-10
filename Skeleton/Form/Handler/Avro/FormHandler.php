<?php
namespace {{ bundle_namespace }}\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use {{ bundle_namespace }}\Entity\{{ entity }};
use {{ bundle_namespace }}\Entity\{{ entity }}Manager;

/*
 * {{ entity | camelCaseToTitle }} Form Handler
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class {{ entity }}FormHandler
{
    protected $form;
    protected $request;
    protected ${{ entity_cc }}Manager;

    public function __construct(Form $form, Request $request, {{ entity }}Manager ${{ entity_cc }}Manager)
    {
        $this->form = $form;
        $this->request = $request;  
        $this->{{ entity_cc }}Manager = ${{ entity_cc }}Manager;
    }

    /*
     * Process the form
     *
     * @param {{ entity }} 
     *
     * @return boolean true if successful
     * @return array $errors if unsuccessful
     */
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
            }
        }

        return false;
    }

    /*
     * Update {{ entity }} if valid
     *
     * @param {{ entity }}
     */
    protected function onSuccess({{ entity }} ${{ entity_cc }})
    {
        $this->{{ entity_cc }}Manager->update(${{ entity_cc }});
    }
}
